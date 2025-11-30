<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Models\Agent;
use App\Models\Event;
use App\Models\Account;
use App\Models\Property;
use App\Models\EventInvite;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PemiluPilihan;
use App\Models\InformasiKlien;
use App\Models\PropertyInterest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\TransactionCommission;

class AgentAdminController extends Controller
{
    private int $intervalSeconds = 300;
    public function markAsSold(Request $request, $id)
{
    $property = Property::findOrFail($id);

    if (strcasecmp($property->status, 'Tersedia') === 0) {
        $property->status = 'Terjual';
        $property->tanggal_diupdate = Carbon::now();
        $property->save();
    }

    // Kembali ke URL asal (beserta query & tab)
    $to = $request->input('redirect') ?: url()->previous();

    return redirect()->to($to)->with('status', 'Properti berhasil diubah menjadi Terjual');
}
public function index()
{
    $idAccount = session('id_account');
    $role = session('role');

    $idAgent = null;

    if ($role === 'Agent') {
        $idAgent = DB::table('agent')
            ->where('id_account', $idAccount)
            ->value('id_agent');
    }

    // Target agent yang dipakai untuk filter transaksi & komisi
    $targetAgentId = $idAgent ?? $idAccount;

    // TOTAL KOMISI: sekarang ambil dari transaction_commissions.pendapatan
    $totalKomisi = DB::table('transaction_commissions')
        ->where('id_agent', $targetAgentId)
        ->sum('pendapatan');

    // TOTAL SELISIH masih dari tabel transaction (kolom selisih masih ada)
    $totalSelisih = DB::table('transaction')
        ->where('id_agent', $targetAgentId)
        ->sum('selisih');

    $jumlahListing = 0;
    $jumlahClients = 0;
    $clients = collect();
    $clientsClosing = collect();
    $clientsPengosongan = collect();
    $statusCounts = [
        'followup' => 0,
        'pending' => 0,
        'buyer_meeting' => 0,
        'gagal' => 0,
        'closing' => 0,
    ];
    $pendingAgents = collect();

    if ($role === 'Owner') {
        $pendingAgents = DB::table('account')
            ->where('account.roles', 'Pending')
            ->select('id_account', 'username', 'nama', 'nomor_telepon')
            ->get();
    }

    if ($role === 'Agent') {
        $statusCounts = DB::table('property_interests')
            ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
            ->where('property.id_agent', $idAgent)
            ->whereNotIn('property_interests.status', [
                'closing',
                'kutipan_risalah_lelang',
                'akte_grosse',
                'balik_nama'
            ])
            ->selectRaw("
                SUM(CASE WHEN property_interests.status = 'FollowUp' THEN 1 ELSE 0 END) as followup,
                SUM(CASE WHEN property_interests.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN property_interests.status = 'gagal' THEN 1 ELSE 0 END) as gagal,
                SUM(CASE WHEN property_interests.status = 'buyer_meeting' THEN 1 ELSE 0 END) as buyer_meeting
            ")
            ->first();

        $jumlahListing = DB::table('property')
            ->where('id_agent', $idAgent)
            ->count();

        $jumlahClients = DB::table('property_interests')
            ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
            ->where('property.id_agent', $idAgent)
            ->distinct('property_interests.id_klien')
            ->count('property_interests.id_klien');

        $clients = DB::table('property_interests')
            ->join('account', 'property_interests.id_klien', '=', 'account.id_account')
            ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
            ->leftJoin('informasi_klien', 'account.id_account', '=', 'informasi_klien.id_account')
            ->where('property.id_agent', $idAgent)
            ->whereNotIn(DB::raw('LOWER(TRIM(property_interests.status))'), ['closing', 'balik_nama', 'akte_grosse', 'gagal'])
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.lokasi',
                'property.harga',
                'property_interests.status',
                'informasi_klien.gambar_ktp'
            )
            ->get();
    }

    if ($role === 'Register') {
        $clientsClosing = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->join('property', 'transaction.id_listing', '=', 'property.id_listing')
            ->whereIn('transaction.status_transaksi', [
                'Closing', 'Kuitansi', 'Kode Billing', 'Kutipan Risalah Lelang', 'Akte Grosse'
            ])
            ->where('transaction.status_transaksi', '!=', 'Balik Nama')
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.lokasi',
                'property.harga',
                'transaction.status_transaksi as status',
                'transaction.tanggal_diupdate'
            )
            ->orderBy('transaction.tanggal_diupdate', 'asc')
            ->get();
    }

    if ($role === 'Pengosongan') {
        $clientsPengosongan = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->join('property', 'transaction.id_listing', '=', 'property.id_listing')
            ->whereIn('transaction.status_transaksi', [
                'Balik Nama', 'Eksekusi Pengosongan', 'Selesai'
            ])
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.lokasi',
                'property.harga',
                'transaction.status_transaksi as status',
                'transaction.tanggal_diupdate'
            )
            ->orderBy('transaction.tanggal_diupdate', 'asc')
            ->get();
    }

    // **Penambahan untuk Stoker**
    if ($role === 'Stoker') {
        $stokerProperties = Property::select(
            'id_listing','lokasi','luas','harga','gambar','status',
            'tipe','provinsi','kota','kecamatan','vendor' // << tambahkan vendor
        )
        ->whereRaw('LOWER(status) = ?', ['tersedia'])
        ->when(request('search'), function ($q, $search) {
            return is_numeric($search)
                ? $q->where('id_listing', (int)$search)
                : $q->whereRaw('1=0');
        })
        ->when(request('vendor'), function ($q, $v) {
            $v = mb_strtolower(trim($v), 'UTF-8');
            return $q->whereRaw('LOWER(vendor) LIKE ?', ['%'.$v.'%']);
        })
        ->when(request('property_type'), fn($q,$v) => $q->whereRaw('LOWER(tipe)=?', [strtolower($v)]))
        ->when(request('province'), fn($q,$v) => $q->where('provinsi', $v))
        ->when(request('city'), fn($q,$v) => $q->where('kota', $v))
        ->when(request('district'), fn($q,$v) => $q->where('kecamatan', $v))
        ->orderByDesc('id_listing')
        ->paginate(10)
        ->appends(request()->only(['search','vendor','property_type','province','city','district']));
    }

    $salesData = $salesData ?? [];
    $transactions = $transactions ?? [];
    // Siapkan label bulan
    $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Ambil pendapatan & jumlah transaksi (berdasarkan selisih per bulan)
    $earnings = DB::table('transaction')
        ->select(
            DB::raw('EXTRACT(YEAR FROM tanggal_transaksi) AS year'),
            DB::raw('EXTRACT(MONTH FROM tanggal_transaksi) AS month'),
            DB::raw('SUM(selisih) AS total'),
            DB::raw('COUNT(*) as total_transaksi')
        )
        ->where('id_agent', $targetAgentId)
        ->groupByRaw('EXTRACT(YEAR FROM tanggal_transaksi), EXTRACT(MONTH FROM tanggal_transaksi)')
        ->orderByRaw('EXTRACT(YEAR FROM tanggal_transaksi), EXTRACT(MONTH FROM tanggal_transaksi)')
        ->get();

    $revenue = array_fill(0, 12, 0);
    $transactions = array_fill(0, 12, 0);
    foreach ($earnings as $e) {
        $revenue[$e->month - 1] = (int)$e->total;
        $transactions[$e->month - 1] = (int)$e->total_transaksi;
    }

    // Status klien untuk pie chart
    $statusCounts = DB::table('property_interests')
        ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
        ->where('property.id_agent', $idAgent)
        ->selectRaw("
            SUM(CASE WHEN property_interests.status = 'FollowUp' THEN 1 ELSE 0 END) as followup,
            SUM(CASE WHEN property_interests.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN property_interests.status = 'gagal' THEN 1 ELSE 0 END) as gagal,
            SUM(CASE WHEN property_interests.status = 'buyer_meeting' THEN 1 ELSE 0 END) as buyer_meeting,
            SUM(CASE WHEN property_interests.status = 'closing' THEN 1 ELSE 0 END) as closing
        ")
        ->first();

    //calender
    $idAccount = session('id_account') ?? Cookie::get('id_account');
    // Event yang dia buat
    $myEvents = Event::where('created_by', $idAccount);

    // Event yang dia diundang
    $invitedEvents = Event::whereHas('invites', function ($q) use ($idAccount) {
        $q->where('id_account', $idAccount);
    });

    // Event dengan akses terbuka
    $publicEvents = Event::where('akses', 'Terbuka');

    // Gabungkan ketiga query
    $events = $myEvents
        ->union($invitedEvents)
        ->union($publicEvents)
        ->get();

    $events = Event::leftJoin('account', 'account.id_account', '=', 'events.created_by')
    ->select(
        'events.*',
        'account.nama as creator_name'
    )
    ->get();

    // Format untuk JS
    $eventsFormatted = $events->map(function ($event) {
        return [
            'id'       => $event->id_event,
            'title'    => $event->title,
            'description' => $event->description,
            'start'    => Carbon::parse($event->mulai)->format('Y-m-d\TH:i:s'),
            'end'      => $event->selesai ? Carbon::parse($event->selesai)->format('Y-m-d\TH:i:s') : null,
            'allDay'   => (bool) $event->all_day,
            'location' => $event->location,
            'access'   => $event->akses,
            'created_by'  => $event->creator_name,
            'created_by_id' => $event->created_by,
            'duration' => $event->durasi,
        ];
    });

    // Fetch sold properties directly from the property table
    $soldProperties = DB::table('property')
        ->where('status', 'Terjual') // Only fetch sold properties
        ->orderBy('tanggal_diupdate', 'desc') // Order by update date
        ->select('id_listing', 'lokasi', 'tanggal_diupdate') // Select only the needed fields
        ->get();

    // Kirim ke view
    return view('Agent.dashboard-agent', [
        'totalKomisi'      => $totalKomisi,
        'totalSelisih'     => $totalSelisih,
        'jumlahListing'    => $jumlahListing,
        'jumlahClients'    => $jumlahClients,
        'clients'          => $clients,
        'stokerProperties' => $stokerProperties ?? null,
        'soldProperties'   => $soldProperties,
        'clientsClosing'   => $clientsClosing,
        'clientsPengosongan' => $clientsPengosongan,
        'properties'       => $properties ?? null,
        'salesData'        => json_encode($salesData),         // <--- dijamin ada
        'transactions'     => json_encode($transactions),
        'labels'           => $labels,
        'revenue'          => $revenue,
        'transactions'     => $transactions,
        'statusCounts'     => (array) $statusCounts,
        'pendingAgents'    => $pendingAgents,
        'events'           => $eventsFormatted,
        'soldProperties'   => $soldProperties, // <-- Make sure to include this inside the main array
    ]);
}


    public function updateInvite(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id_event',
            'status'   => 'required|string|in:join',
            'access'   => 'required|in:Terbuka,Tertutup,terbuka,tertutup',
        ]);

        $accountId = session('id_account') ?? Cookie::get('id_account');
        if (!$accountId) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        $event = Event::findOrFail($validated['event_id']);

        // ===== Case khusus PEMILU saja =====
        if (strtolower($event->title) !== 'pemilu') {
            return response()->json([
                'status' => 'error',
                'message' => 'Update undangan hanya berlaku untuk event Pemilu.'
            ], 400);
        }

        // --- Validasi jika waktu sudah melewati waktu mulai ---
        $currentTime = Carbon::now();
        $eventStartTime = Carbon::parse($event->mulai); // Ambil waktu mulai event

        // Cek apakah waktu sudah lewat
        if ($currentTime->gt($eventStartTime)) {
            // Jika sudah lewat dan user belum terdaftar, beri pesan pendaftaran ditutup
            $existingInvite = EventInvite::where('id_event', $event->id_event)
                ->where('id_account', $accountId)
                ->first();

            if (!$existingInvite) {
                return redirect()->back()->with('error', 'Pendaftaran sudah ditutup. Event telah dimulai.');
            }
        }

        // === cek apakah user sudah terdaftar di event ini ===
        $existingInvite = EventInvite::where('id_event', $event->id_event)
            ->where('id_account', $accountId)
            ->first();

        // Jika sudah terdaftar, biarkan dia join lagi (rejoin)
        if ($existingInvite) {
            // Jika status giliran sudah 'Hadir', beri pesan bahwa dia bisa rejoin
            if ($existingInvite->status_giliran === 'Hadir') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Anda sudah terdaftar dan hadir, Anda bisa bergabung kembali.'
                ]);
            }

            // Jika terdaftar, tetapi belum hadir, update statusnya menjadi 'Hadir'
            $existingInvite->update([
                'status' => 'Hadir',
                'tanggal_diupdate' => now(),
            ]);

            return redirect()->route('pemilu.show', ['event' => $event->id_event]);
        }

        // === kalau belum punya invite, insert baru ===
        $lastInvite = EventInvite::where('id_event', $event->id_event)
            ->orderByDesc('urutan')
            ->first();

        $nextOrder = $lastInvite ? $lastInvite->urutan + 1 : 1;

        // hitung mulai_giliran & selesai_giliran
        if ($lastInvite && $lastInvite->selesai_giliran) {
            $mulaiGiliran = Carbon::parse($lastInvite->selesai_giliran);
        } else {
            $mulaiGiliran = Carbon::parse($event->mulai);
        }

        $durasiMenit = $event->durasi ?? 0;
        $selesaiGiliran = $mulaiGiliran->copy()->addMinutes($durasiMenit);

        // insert giliran baru
        EventInvite::create([
            'id_event'        => $event->id_event,
            'id_account'      => $accountId,
            'status'          => 'Hadir',
            'urutan'          => $nextOrder,
            'mulai_giliran'   => $mulaiGiliran,
            'selesai_giliran' => $selesaiGiliran,
            'status_giliran'  => 'Menunggu',
            'tanggal_dibuat'  => now(),
            'tanggal_diupdate'=> now(),
        ]);

        return redirect()->route('pemilu.show', ['event' => $event->id_event]);
    }

    public function show(Request $request, $idEvent)
    {
        // 1) Ambil data event berdasarkan idEvent
        $event = DB::table('events')
            ->where('id_event', $idEvent)
            ->first(['id_event', 'mulai', 'selesai', 'durasi']);
        abort_if(!$event, 404, 'Event tidak ditemukan');

        $now = Carbon::now();
        $eventMulai = Carbon::parse($event->mulai);
        $eventSelesai = $event->selesai ? Carbon::parse($event->selesai) : null;
        $slotSeconds = max(1, (int)$event->durasi) * 60;

        // === Tambahan: status event (untuk konsistensi UI) ===
        $eventStatus = 'Belum Mulai';
        if ($eventSelesai && $now->gte($eventSelesai)) {
            $eventStatus = 'Selesai';
        } elseif ($now->gte($eventMulai) && (!$eventSelesai || $now->lt($eventSelesai))) {
            $eventStatus = 'Berjalan';
        }

        // 2) User (opsional)
        $accountId = session('id_account') ?? Cookie::get('id_account');

        // 3) Ambil peserta (pakai urutan)
        $invitesRaw = DB::table('event_invites')
            ->join('account', 'event_invites.id_account', '=', 'account.id_account')
            ->where('event_invites.id_event', $idEvent)
            ->orderBy('event_invites.urutan')
            ->get([
                'event_invites.id_invite',
                'event_invites.id_account',
                'account.username',
                'event_invites.urutan',
                'event_invites.mulai_giliran',
                'event_invites.selesai_giliran',
                'event_invites.status_giliran',
            ]);

        $jumlah = $invitesRaw->count();
        if ($jumlah === 0) {
            return view('Agent.pemilu', [
                'event' => $event,
                'invites' => collect(),
                'properties' => \App\Models\Property::select('id_listing', 'lokasi', 'luas', 'harga', 'gambar')
                    ->where('id_agent', 'AG001')->paginate(10),
                'current' => null,
                'currentTime' => $now,
                'eventStartTime' => $eventMulai,
                'isBerjalan' => false,
                'nextRefreshAtMs' => null,
                'eventStatus' => $eventStatus, // <-- kirim ke view
            ]);
        }

        // 4) Hitung siklus aktif dari event.mulai
        $cycleSeconds = $slotSeconds * $jumlah;
        $cycleIndex = 0;
        if ($now->gte($eventMulai) && $cycleSeconds > 0) {
            $cycleIndex = intdiv($eventMulai->diffInSeconds($now), $cycleSeconds);
        }

        // 5) Bentuk data aktif & (nanti) tulis balik ke DB
        $invites = $invitesRaw->map(function ($invite) use ($cycleIndex, $jumlah, $slotSeconds, $eventMulai, $eventSelesai, $now) {
            // slot global sejak event.mulai
            $slotGlobal = $cycleIndex * $jumlah + ($invite->urutan - 1);

            $mulaiAktif = (clone $eventMulai)->addSeconds($slotGlobal * $slotSeconds);
            $selesaiAktif = (clone $mulaiAktif)->addSeconds($slotSeconds);

            // === Tambahan: batasi akhir slot ke batas event selesai ===
            $endBound = $eventSelesai ? ($selesaiAktif->lt($eventSelesai) ? $selesaiAktif : (clone $eventSelesai)) : $selesaiAktif;

            if ($eventSelesai && $now->gte($eventSelesai)) {
                // Event sudah selesai -> paksa selesai
                $status = 'Selesai';
                $waktuTersisa = 0;
            } elseif ($now->between($mulaiAktif, $endBound)) {
                $status = 'Berjalan';
                $waktuTersisa = $now->diffInSeconds($endBound);
            } elseif ($now->lt($mulaiAktif)) {
                $status = 'Menunggu';
                $waktuTersisa = 0;
            } else {
                $status = 'Selesai';
                $waktuTersisa = 0;
            }

            // ====== properti untuk render realtime ======
            $invite->mulai_aktif = $mulaiAktif;
            $invite->selesai_aktif = $endBound;          // <-- pakai endBound yang sudah di-clamp
            $invite->status_now = $status;
            $invite->status_giliran = $status;           // <-- sinkron agar Blade pakai status_giliran saja
            $invite->waktu_tersisa = $waktuTersisa;

            return $invite;
        });

        // 5b) Tulis balik ke event_invites (tanpa histori)
        if (!$eventSelesai || $now->lt($eventSelesai)) {
            foreach ($invites as $i) {
                // Kurangi write jika tidak berubah
                $newMulai = $i->mulai_aktif->toDateTimeString();
                $newSelesai = $i->selesai_aktif->toDateTimeString();

                $changed = ($i->mulai_giliran ?? null) !== $newMulai
                    || ($i->selesai_giliran ?? null) !== $newSelesai
                    || ($i->status_giliran ?? null) !== $i->status_now;

                if ($changed) {
                    DB::table('event_invites')
                        ->where('id_invite', $i->id_invite)
                        ->update([
                            'mulai_giliran' => $newMulai,
                            'selesai_giliran' => $newSelesai,
                            'status_giliran' => $i->status_now,
                            'tanggal_diupdate' => now(),
                        ]);
                }
            }
        }

        // 6) Siapa yang sedang berjalan? (pakai status_giliran yang sudah disinkron)
        $current = $invites->firstWhere('status_giliran', 'Berjalan');

        // === Tambahan: kalau event selesai, jangan ada current lagi ===
        if ($eventSelesai && $now->gte($eventSelesai)) {
            $current = null;
        }

        // 7) Next refresh tepat di boundary slot berikutnya (berbasis event.mulai)
        $nextRefresh = null;
        if ($eventSelesai && $now->gte($eventSelesai)) {
            $nextRefresh = null;
        } else {
            if ($now->lt($eventMulai)) {
                $nextRefresh = $eventMulai;
            } else {
                $slotsSinceStart = intdiv($eventMulai->diffInSeconds($now), $slotSeconds) + 1;
                $nextRefresh = (clone $eventMulai)->addSeconds($slotsSinceStart * $slotSeconds);

                // ⬇️ Perbaikan: kalau boundary berikutnya >= waktu selesai event,
                // jadwalkan refresh tepat di waktu event selesai (bukan null).
                if ($eventSelesai && $nextRefresh->gte($eventSelesai)) {
                    $nextRefresh = (clone $eventSelesai);
                }
            }
        }
        $nextRefreshAtMs = $nextRefresh ? $nextRefresh->timestamp * 1000 : null;

        // ⬇️ Kirim juga waktu selesai event sebagai target final (kalau masih di masa depan)
        $eventEndAtMs = ($eventSelesai && $now->lt($eventSelesai)) ? $eventSelesai->timestamp * 1000 : null;


        // 8) isBerjalan untuk akun login
        $isBerjalan = false;
        if ($accountId) {
            $isBerjalan = (bool)$invites
                ->first(fn($i) => $i->id_account === $accountId && $i->status_giliran === 'Berjalan');
        }

// 9) Properties (contoh)
// ambil parameter
$search        = trim($request->get('search', ''));           // ID Listing (exact)
$propertyType  = $request->get('property_type');              // kolom: tipe
$province      = $request->get('province');                   // kolom: provinsi
$city          = $request->get('city');                       // kolom: kota
$district      = $request->get('district');                   // kolom: kecamatan

// Jika user isi ID Listing, abaikan filter lokasi dan tipe property
if ($search !== '') {
    $propertyType = $province = $city = $district = null;  // Reset filter lokasi dan tipe
}

$properties = Property::select('id_listing', 'lokasi', 'luas', 'harga', 'gambar')
    ->where('id_agent', 'AG001')
    ->when($search !== '', function ($q) use ($search) {
        // id_listing integer → exact match
        return $q->where('id_listing', (int) $search);
    })
    ->when($propertyType, function ($q) use ($propertyType) {
        return $q->whereRaw('LOWER(tipe) = ?', [strtolower($propertyType)]);
    })
    ->when($province, function ($q) use ($province) {
        return $q->where('provinsi', $province);
    })
    ->when($city, function ($q) use ($city) {
        return $q->where('kota', $city);
    })
    ->when($district, function ($q) use ($district) {
        return $q->where('kecamatan', $district);
    })
    ->paginate(10)
    ->appends($request->only(['search','property_type','province','city','district']));



        // Ambil log transaksi
        $logs = PemiluPilihan::where('id_event', $idEvent)->get();

        // Ambil nama agent untuk setiap log yang punya id_agent
        $logs = $logs->map(function ($log) {
            $log->agent_name = optional(Agent::find($log->id_agent))->nama; // nama agent
            return $log;
        });

        return view('Agent.pemilu', [
            'event' => $event,
            'invites' => $invites,          // ->mulai_aktif, ->selesai_aktif, ->status_giliran
            'properties' => $properties,
            'current' => $current,
            'currentTime' => $now,
            'eventStartTime' => $eventMulai,
            'isBerjalan' => $isBerjalan,
            'nextRefreshAtMs' => $nextRefreshAtMs,
            'accountId' => $accountId,
            'logs' => $logs,
            'eventStatus' => $eventStatus,
            'eventEndAtMs'    => $eventEndAtMs, // ⬅️ tambahan   // <-- kirim ke view
        ]);
    }




    public function pilihProperty(Request $request, $eventId, $listingId)
    {
        $accountId = session('id_account') ?? Cookie::get('id_account');
        if (!$accountId) {
            return back()->with('error', 'User tidak terautentikasi.');
        }

        // cari id_agent dari tabel agent
        $agent = Agent::where('id_account', $accountId)->first();
        if (!$agent) {
            return back()->with('error', 'Akun ini tidak terhubung dengan agent.');
        }

        $idAgent = $agent->id_agent;

        // update property → set id_agent + tanggal_diupdate
        Property::where('id_listing', $listingId)->update([
            'id_agent'        => $idAgent,
            'tanggal_diupdate'=> now(), // pakai timezone laravel (APP_TIMEZONE)
        ]);

        // create ke pemilu_pilihan
        PemiluPilihan::create([
            'id_event'   => $eventId,
            'id_agent'   => $idAgent,
            'id_listing' => $listingId,
        ]);

        return back()->with('success', 'Property berhasil dipilih.');
    }


    // Endpoint JSON untuk polling UI kanan-atas
    public function state($idEvent)
    {
        $event = DB::table('events')->where('id_event', $idEvent)->first(['id_event','mulai']);
        abort_if(!$event, 404);

        $invites = EventInvite::forEvent($idEvent)->orderBy('urutan')->get([
            'id_account','urutan','mulai_giliran','selesai_giliran'
        ]);

        $now = now();
        $current = null;
        foreach ($invites as $v) {
            if ($now->between($v->mulai_giliran, $v->selesai_giliran)) {
                $current = $v;
                break;
            }
        }

        return response()->json([
            'current' => $current,
            'invites' => $invites,
            'server_time' => $now->toDateTimeString(),
        ]);
    }

    public function owner()
    {
        $tab = request('tab', 'stoker');
        //property types
        // Semua tipe properti yang mau ditampilkan
        $propertyTypes = ['Rumah', 'Gudang', 'Apartemen', 'Tanah', 'Pabrik', 'Hotel dan Villa', 'Ruko', 'Toko'];

        // Ambil count dari DB per tipe (hanya yang Tersedia)
        $propertyCounts = DB::table('property')
            ->selectRaw("LOWER(tipe) as tipe, COUNT(*) as total")
            ->where('status', 'Tersedia') // hitung hanya properti yang tersedia
            ->groupBy('tipe')
            ->pluck('total', 'tipe') // hasilnya array: ['rumah' => 5, 'villa' => 2, ...]
            ->toArray();

        // Gabungkan semua tipe dengan count-nya
        $properties = collect($propertyTypes)->map(function ($type) use ($propertyCounts) {
            $lowerType = strtolower($type);
            return (object)[
                'tipe' => $type,
                'total' => $propertyCounts[$lowerType] ?? 0 // default 0 jika tidak ada
            ];
        });

        //pending agents
        $pendingAgents = DB::table('agent')
            ->join('account', 'agent.id_account', '=', 'account.id_account')
            ->where('agent.status', 'Pending')
            ->select(
                'agent.id_account',
                'account.username',
                'account.nama',
                'account.nomor_telepon',
                'agent.gambar_ktp',
                'agent.gambar_npwp'
            )
            ->get();

        //pending clients
        $pendingClients = DB::table('informasi_klien')
            ->join('account', 'informasi_klien.id_account', '=', 'account.id_account')
            ->where('informasi_klien.status_verifikasi', 'Pending')
            ->select(
                'informasi_klien.id_account',
                'account.nama',
                'account.email',
                'informasi_klien.gambar_ktp',
                'informasi_klien.gambar_npwp'
            )
            ->get();

        //progress agent
        $clients = DB::table('property_interests')
            ->join('account', 'property_interests.id_klien', '=', 'account.id_account')
            ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
            ->leftJoin('informasi_klien', 'account.id_account', '=', 'informasi_klien.id_account')
            ->whereNotIn(DB::raw('LOWER(TRIM(property_interests.status))'), ['closing', 'balik_nama', 'akte_grosse', 'gagal']) // cek lowercase dan trim
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.id_agent',
                'property.lokasi',
                'property.harga',
                'property_interests.status',
                'informasi_klien.gambar_ktp'
            )
            ->get();

        // progress register
        $clientsClosing = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->join('property', 'transaction.id_listing', '=', 'property.id_listing')
            ->whereIn('transaction.status_transaksi', [
                'Closing',
                'Kuitansi',
                'Kode Billing',
                'Kutipan Risalah Lelang',
                'Akte Grosse'
            ]) // ✅ HAPUS "Balik Nama" dari sini
            ->where('transaction.status_transaksi', '!=', 'Balik Nama') // ✅ FILTER yang sudah selesai
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.lokasi',
                'property.harga',
                'transaction.status_transaksi as status',
                'transaction.tanggal_diupdate'
            )
            ->orderBy('transaction.tanggal_diupdate', 'asc')
            ->get();

        // progress pengosongan
        $clientsPengosongan = DB::table('transaction')
            ->join('account', 'transaction.id_klien', '=', 'account.id_account')
            ->join('property', 'transaction.id_listing', '=', 'property.id_listing')
            ->whereIn('transaction.status_transaksi', [
                'Balik Nama',
                'Eksekusi Pengosongan',
                'Selesai',
            ])
            ->select(
                'account.id_account',
                'account.nama',
                'account.nomor_telepon',
                'property.id_listing',
                'property.lokasi',
                'property.harga',
                'transaction.status_transaksi as status',
                'transaction.tanggal_diupdate'
            )
            ->orderBy('transaction.tanggal_diupdate', 'asc')
            ->get();

        // ---------- BLOK STOKER (SELALU DISIAPKAN, TIDAK TERGANTUNG ROLE) ----------
        $stokerProperties = Property::select(
            'id_listing','lokasi','luas','harga','gambar','status',
            'tipe','provinsi','kota','kecamatan',
            'vendor' // <- WAJIB, dipakai di partial
        )
            ->whereRaw('LOWER(status) = ?', ['tersedia'])
            ->when(request('search'), function ($query, $search) {
                return is_numeric($search)
                    ? $query->where('id_listing', (int)$search)
                    : $query->whereRaw('1=0');
            })
            ->when(request('property_type'), fn($q,$v) => $q->whereRaw('LOWER(tipe)=?', [strtolower($v)]))
            ->when(request('province'), fn($q,$v) => $q->where('provinsi', $v))
            ->when(request('city'), fn($q,$v) => $q->where('kota', $v))
            ->when(request('district'), fn($q,$v) => $q->where('kecamatan', $v))
            ->orderByDesc('id_listing')
            ->paginate(10)
            ->appends(array_merge(
                request()->only(['search','property_type','province','city','district']),
                ['tab'=>'stoker']
            ));


        $soldProperties = DB::table('property')
            ->where('status', 'Terjual')
            ->orderBy('tanggal_diupdate', 'desc')
            ->select('id_listing', 'lokasi', 'tanggal_diupdate')
            ->limit(15)
            ->get();

        // ---------- BLOK TRANSAKSI (UNTUK TAB "Transaksi") ----------
        $transaksiProperties = DB::table('property')
            ->select(
                'property.id_listing',
                'property.lokasi',
                'property.tipe',
                'property.luas',
                'property.harga',
                'property.gambar',
                'property.status',
                'property.tanggal_diupdate',
                DB::raw('NULL::integer as id_transaksi')
            )
            ->when(request('search'), function ($query, $search) {
                return is_numeric($search)
                    ? $query->where('property.id_listing', (int)$search)
                    : $query->whereRaw('1=0');
            })
            ->when(request('vendor'), function ($q, $v) {
                return $q->where('property.vendor', 'ILIKE', '%'.$v.'%');
                // kalau MySQL: ganti 'ILIKE' -> 'like'
            })
            ->when(request('property_type'), function ($q, $v) {
                return $q->whereRaw('LOWER(property.tipe) = ?', [strtolower($v)]);
            })
            ->when(request('province'), fn($q,$v) => $q->where('property.provinsi', $v))
            ->when(request('city'),     fn($q,$v) => $q->where('property.kota', $v))
            ->when(request('district'), fn($q,$v) => $q->where('property.kecamatan', $v))
            ->orderByDesc('property.tanggal_diupdate')
            ->paginate(10)
            ->appends(array_merge(
                request()->only(['search','vendor','property_type','province','city','district']),
                ['tab'=>'transaksi']
            ));

        // 🔥🔥 COPIC MAP: id_listing -> daftar agent yang pernah pegang aset yang sama (untuk bagi CO PIC 0.25%)
        $copicAgentsMap = [];

        if ($transaksiProperties->count() > 0) {
            foreach ($transaksiProperties as $p) {
                /** @var \App\Models\Property|null $current */
                $current = \App\Models\Property::find((int) $p->id_listing);
                if (!$current) {
                    continue;
                }

                // --- Normalisasi sertifikat: huruf + angka saja, lowercase ---
                $sertKey = null;
                if (!empty($current->sertifikat)) {
                    $lower  = mb_strtolower($current->sertifikat, 'UTF-8');
                    $sertKey = preg_replace('/[^a-z0-9]/u', '', $lower);
                }

                // --- Ambil semua listing lain dengan aset yang sama ---
                $history = \App\Models\Property::query()
                    ->where('id_listing', '!=', $current->id_listing)
                    ->when($sertKey, function ($q) use ($sertKey) {
                        $q->whereRaw(
                            "regexp_replace(lower(coalesce(sertifikat, '')), '[^a-z0-9]', '', 'g') = ?",
                            [$sertKey]
                        );
                    })
                    ->when($current->luas, function ($q) use ($current) {
                        $q->where('luas', $current->luas);
                    })
                    ->when($current->kota, function ($q) use ($current) {
                        $q->whereRaw('LOWER(TRIM(kota)) = ?', [strtolower(trim($current->kota))]);
                    })
                    ->get();

                // gabungkan current + history
                $rows = collect([$current])->merge($history);

                // kumpulkan semua id_agent unik yang pernah pegang aset ini
                $agentIds = $rows->pluck('id_agent')->filter()->unique()->values();
                if ($agentIds->isEmpty()) {
                    continue;
                }

                // ambil nama agent
                $agentNames = DB::table('agent')
                    ->whereIn('id_agent', $agentIds->all())
                    ->pluck('nama', 'id_agent')
                    ->toArray();

                $copicAgentsMap[$current->id_listing] = [
                    'ids'   => $agentIds->all(),             // contoh: ['AG003','AG001','AG008']
                    'names' => array_values($agentNames),    // contoh: ['Jason ...','Lie Ming', ...]
                ];
            }
        }

        // Riwayat singkat (kanan): terakhir update transaksi
        // Riwayat singkat (kanan): terakhir update transaksi
        $transaksiHistory = DB::table('transaction as trx')
        ->join('property as p', 'p.id_listing', '=', 'trx.id_listing')
        ->leftJoin('agent   as a', 'a.id_agent', '=', 'trx.id_agent')
        ->leftJoin('account as c', 'c.id_account', '=', 'trx.id_klien')
        ->select([
            'trx.id_transaction',
            'trx.skema_komisi',
            'trx.id_agent',
            'trx.id_klien',
            'trx.id_listing',
            'trx.harga_limit',
            'trx.harga_bidding',
            'trx.selisih',
            'trx.persentase_komisi',
            'trx.basis_pendapatan',
            'trx.biaya_baliknama',
            'trx.biaya_pengosongan',
            'trx.status_transaksi as status',
            'trx.tanggal_transaksi',
            'trx.kenaikan_dari_limit',

            'p.lokasi',
            'p.gambar',
            'p.tipe',
            'p.harga',

            'a.nama as agent_nama',
            'c.nama as client_nama',
        ])
        ->orderByDesc('trx.tanggal_transaksi')
        ->limit(20)
        ->get();

        $clientsDropdown = DB::table('account')
            ->where('roles', 'User')
            ->select('id_account','nama')
            ->orderBy('nama')
            ->get();

        // Aggregate: jumlah "Hadir" per account
        // 1) Ikut Pemilu: jumlah "Hadir" per account
        $eiStats = DB::table('event_invites')
            ->select(
                'id_account',
                DB::raw("SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) AS ikut_pemilu_count")
            )
            ->groupBy('id_account');

        // 2) Share Listing: agregasi referral clicks per agent
        $referralStats = DB::table('referral_clicks')
            ->select(
                'id_agent',
                DB::raw('COUNT(*) AS share_clicks'),                         // total klik
                DB::raw('COUNT(DISTINCT id_listing) AS share_listing_uniq'), // jumlah listing unik
                DB::raw('MAX(created_at) AS last_share_click_at')           // opsional: terakhir klik
            )
            ->groupBy('id_agent');

        // 2b) Komisi: agregasi dari transaction_commissions
        $commissionStats = DB::table('transaction_commissions')
            ->select(
                'id_agent',
                DB::raw('SUM(pendapatan) AS total_komisi')
            )
            ->groupBy('id_agent');

        // 3) Query utama: hanya agent Aktif
        $performanceAgents = DB::table('agent')
            ->whereIn('agent.status', ['Aktif', 'Diterminasi'])
            ->leftJoin('property', 'agent.id_agent', '=', 'property.id_agent')
            ->leftJoin('transaction', 'agent.id_agent', '=', 'transaction.id_agent')

            // map event_invites.id_account → agent.id_account
            ->leftJoinSub($eiStats, 'ei_stat', function ($join) {
                $join->on('ei_stat.id_account', '=', 'agent.id_account');
            })

            // join agregat referral clicks
            ->leftJoinSub($referralStats, 'ref_stat', function ($join) {
                $join->on('ref_stat.id_agent', '=', 'agent.id_agent');
            })

            // join agregat komisi dari transaction_commissions
            ->leftJoinSub($commissionStats, 'komisi_stat', function ($join) {
                $join->on('komisi_stat.id_agent', '=', 'agent.id_agent');
            })

            ->select(
                'agent.id_account',
                'agent.id_agent',
                'agent.nama',
                'agent.status',
                'agent.jumlah_penjualan',
                DB::raw('COUNT(DISTINCT property.id_listing) AS jumlah_listing'),
                DB::raw('COALESCE(MAX(komisi_stat.total_komisi), 0) AS total_komisi'),

                // Ikut Pemilu (angka)
                DB::raw('MAX(COALESCE(ei_stat.ikut_pemilu_count, 0)) AS ikut_pemilu'),

                // Share Listing (pakai total klik)
                DB::raw('MAX(COALESCE(ref_stat.share_clicks, 0)) AS share_listing'),

                // Opsional kalau nanti mau dipakai:
                DB::raw('MAX(COALESCE(ref_stat.share_listing_uniq, 0)) AS share_listing_uniq'),
                DB::raw('MAX(ref_stat.last_share_click_at) AS last_share_click_at')
            )
            ->groupBy(
                'agent.id_agent',
                'agent.nama',
                'agent.status',
                'agent.jumlah_penjualan',
                'agent.id_account'
            )
            ->get();

        //grafik
        $monthlyData = DB::table('transaction')
            ->selectRaw("DATE_TRUNC('month', tanggal_dibuat) as bulan, SUM(basis_pendapatan) as total_pendapatan, COUNT(*) as total_transaksi")
            ->groupByRaw("DATE_TRUNC('month', tanggal_dibuat)")
            ->orderByRaw("DATE_TRUNC('month', tanggal_dibuat)")
            ->get();

        $performanceClients = DB::table('account')
            ->leftJoin('informasi_klien as ik', 'ik.id_account', '=', 'account.id_account')
            ->leftJoin('agent', 'agent.id_agent', '=', 'account.kode_referal')
            ->where('account.roles', 'User') // ⬅️ hanya akun ber-role User
            ->select(
                'account.id_account',
                'account.nama',
                'account.kode_referal',
                DB::raw("COALESCE(agent.nama, '-') AS nama_agent"),
                DB::raw("COALESCE(account.kota, '-') AS kota"),
                'ik.pekerjaan',
                'ik.status_verifikasi'
            )
            ->orderBy('account.id_account')
            ->get();


        // ---------- BLOK EXPORT (bersih, tanpa join tabel fiktif) ----------
        $exportQuery = \App\Models\Property::from('property as p')
            ->select([
                // Kolom untuk TAMPIL di tabel halaman Export
                'p.id_listing',          // ditampilkan sebagai ID
                'p.lokasi',
                'p.tipe',
                'p.luas',
                'p.harga',
                'p.gambar',              // dipakai Blade: explode(',', $property->gambar)

                // Kolom tambahan untuk EXPORT file
                'p.sertifikat',
                'p.id_agent',
                'p.link',

                // Link Solusindo (PostgreSQL concatenation pakai ||)
                DB::raw("('https://solusindolelang.com/property-detail/' || p.id_listing || '/' || COALESCE(p.id_agent, '')) as link_solusindo")
            ])

            // Filter (opsional: aktifkan jika mau hanya status 'Tersedia')
            // ->whereRaw('LOWER(p.status) = ?', ['tersedia'])

            ->when(request('search'), function ($query, $search) {
                return is_numeric($search)
                    ? $query->where('p.id_listing', (int)$search)
                    : $query->whereRaw('1=0'); // cuma izinkan numerik untuk ID
            })
            ->when(request('property_type'), fn($q,$v) => $q->whereRaw('LOWER(p.tipe)=?', [strtolower($v)]))
            ->when(request('province'),      fn($q,$v) => $q->where('p.provinsi', $v))
            ->when(request('city'),          fn($q,$v) => $q->where('p.kota', $v))
            ->when(request('district'),      fn($q,$v) => $q->where('p.kecamatan', $v));

        $exportProperties = $exportQuery
            ->orderBy('p.id_listing', 'asc')
            ->paginate(15)
            ->appends(array_merge(
                request()->only(['search','property_type','province','city','district']),
                ['tab'=>'export']
            ));


        $labels = $monthlyData->map(fn($d) => \Carbon\Carbon::parse($d->bulan)->isoFormat('MMM YYYY'));
        $revenue = $monthlyData->pluck('total_pendapatan');
        $transactions = $monthlyData->pluck('total_transaksi');

        //piechart
        $statusCounts = PropertyInterest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        //calendar
        $idAccount = session('id_account') ?? Cookie::get('id_account');
        // Event yang dia buat
        $myEvents = Event::where('created_by', $idAccount);

        // Event yang dia diundang
        $invitedEvents = Event::whereHas('invites', function ($q) use ($idAccount) {
            $q->where('id_account', $idAccount);
        });

        // Event dengan akses terbuka
        $publicEvents = Event::where('akses', 'Terbuka');

        // Gabungkan ketiga query
        $events = $myEvents
            ->union($invitedEvents)
            ->union($publicEvents)
            ->get();

        $events = Event::leftJoin('account', 'account.id_account', '=', 'events.created_by')
            ->select(
                'events.*',
                'account.nama as creator_name'
            )
            ->get();

        // Format untuk JS
        $eventsFormatted = $events->map(function ($event) {
            $accountId = session('id_account') ?? Cookie::get('id_account');
            $invite = $event->invites()
                ->where('id_account', $accountId)
                ->first();
            return [
                'id'       => $event->id_event,
                'title'    => $event->title,
                'description' => $event->description,
                'start'    => Carbon::parse($event->mulai)->format('Y-m-d\TH:i:s'),
                'end'      => $event->selesai ? Carbon::parse($event->selesai)->format('Y-m-d\TH:i:s') : null,
                'allDay'   => (bool) $event->all_day,
                'location' => $event->location,
                'access'   => $event->akses,
                'created_by'  => $event->creator_name,
                'invite_status' => $invite->status ?? null,
                'duration' => $event->durasi,
            ];
        });

        return view('Agent.dashboardowner', [
            'pendingAgents'       => $pendingAgents,
            'pendingClients'      => $pendingClients,
            'clients'             => $clients,
            'clientsClosing'      => $clientsClosing,
            'clientsPengosongan'  => $clientsPengosongan,
            'performanceAgents'   => $performanceAgents,
            'performanceClients'  => $performanceClients,
            'properties'          => $properties,
            'clientsDropdown'     => $clientsDropdown,
            'properties'          => $properties,
            'labels'              => $labels,
            'revenue'             => $revenue,
            'transactions'        => $transactions,
            'statusCounts'        => $statusCounts,
            'stokerProperties'    => $stokerProperties,
            'soldProperties'      => $soldProperties,
            'transaksiProperties' => $transaksiProperties,
            'transaksiHistory'    => $transaksiHistory,
            'tab'                 => $tab,
            'exportProperties'    => $exportProperties,
            'events'              => $eventsFormatted,
            // 🔥 kirim map COPIC ke Blade (id_listing => ['ids'=>[],'names'=>[]])
            'copicAgentsMap'      => $copicAgentsMap,
        ]);
    }



    public function updateStatusAgent(Request $request, string $idAccount)
    {
        $validated = $request->validate([
            'status' => 'required|in:Aktif,Diterminasi',
        ]);

        $newStatus = $validated['status'];
        $newRole   = $newStatus === 'Aktif' ? 'Agent' : 'User';

        // Pastikan id_account ada di kedua tabel
        $exists = DB::table('agent')->where('id_account', $idAccount)->exists()
               && DB::table('account')->where('id_account', $idAccount)->exists();

        if (!$exists) {
            return response()->json(['ok' => false, 'message' => 'Agent/account tidak ditemukan'], 404);
        }

        DB::transaction(function () use ($idAccount, $newStatus, $newRole) {
            DB::table('agent')->where('id_account', $idAccount)->update(['status' => $newStatus]);
            DB::table('account')->where('id_account', $idAccount)->update(['roles'  => $newRole]);
        });

        return response()->json(['ok' => true, 'status' => $newStatus, 'role' => $newRole]);
    }


    public function store(Request $request)
    {
        // validasi sesuai field form
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start'       => 'required|date',
            'end'         => 'nullable|date|after_or_equal:start',
            'allDay'      => 'boolean',
            'access'      => 'required|in:terbuka,tertutup',
            'location'    => 'nullable|string|max:255',
            'duration'    => 'nullable|integer|min:1',
        ]);

        // --- pengecekan khusus untuk "pemilu" ---
        if (strtolower($validated['title']) === 'pemilu') {
            $startDate = \Carbon\Carbon::parse($validated['start'])->startOfDay();
            $endDate   = \Carbon\Carbon::parse($validated['start'])->endOfDay();

            $exists = \App\Models\Event::whereRaw('LOWER(title) = ?', ['pemilu'])
                ->whereBetween('mulai', [$startDate, $endDate])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Event "pemilu" sudah ada di hari tersebut.'
                ], 422);
            }
        }

        // mapping ke field tabel (supaya sesuai kolom database)
        $data = [
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'mulai'            => $validated['start'],
            'selesai'          => $validated['end'] ?? null,
            'all_day'          => $request->boolean('allDay'),
            'akses'            => ucfirst($validated['access']), // jadi "Terbuka"/"Tertutup"
            'location'         => $validated['location'] ?? null,
            'durasi'           => $validated['duration'] ?? null,
            'created_by'       => session('id_account') ?? Cookie::get('id_account'),
            'tanggal_dibuat'   => now(),
            'tanggal_diupdate' => now(),
        ];

        $event = Event::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Event berhasil dibuat',
            'event'   => $event
        ]);
    }

    public function updateevent(Request $request, $id)
    {
        // Cari event berdasarkan id
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'allDay' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'access' => 'required|in:Terbuka,Tertutup',
            'duration' => 'nullable|integer|min:0'
        ]);

        // Update field event
        $event->title = $validated['title'];
        $event->description = $validated['description'] ?? null;
        $event->mulai = $validated['start'];
        $event->selesai = $validated['end'];
        $event->all_day = $request->has('allDay') ? (bool)$request->allDay : false;
        $event->location = $validated['location'] ?? null;
        $event->akses = $validated['access'];
        $event->durasi = $validated['duration'] ?? null;
        $event->tanggal_diupdate = now();

        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil diperbarui',
            'event' => $event
        ]);
    }


    public function storeregister(Request $request)
    {
            $request->validate([
                'id_account' => 'required',
                'id_listing' => 'required',
                'tahap' => 'required',
                'catatan' => 'required',
            ]);

            DB::table('property_interests')
                ->where('id_account', $request->id_account)
                ->where('id_listing', $request->id_listing)
                ->update([
                    'status' => $request->tahap,
                    'catatan' => $request->catatan,
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true]);
    }

    public function updateToEksekusi(Request $request)
    {
        $id_account = $request->id_account;
        $id_listing = $request->id_listing;

        DB::table('property_interests')
            ->where('id_account', $id_account)
            ->where('id_listing', $id_listing)
            ->update([
                'status' => 'eksekusi_pengosongan',
                'catatan' => 'sedang dilakukan eksekusi pengosongan', // ⬅️ tambahkan ini
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function updateCatatan(Request $request)
    {
        DB::table('property_interests')
            ->where('id_account', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update([
                'catatan' => $request->catatan,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    public function selesaikan(Request $request)
    {
        DB::table('property_interests')
            ->where('id_account', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update([
                'status' => 'selesai',
                'catatan' => '',
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    public function trackProgress(Request $request)
    {
        DB::table('property_interests')
            ->where('id_account', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update(['status' => 'followup', 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function showClosing($id_listing, $id_klien)
    {
        $property = DB::table('property')->where('id_listing', $id_listing)->first();

        // ✅ DEBUG di sini
        // dd($property, $id_listing, $id_klien);

        return view('partial.closing', [
            'property' => $property,
            'id_klien' => $id_klien
        ]);
    }



    public function updateBuyerMeeting(Request $request)
    {
        // Update status & simpan jadwal
        DB::table('property_interests')
            ->where('id_klien', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update([
                'status' => 'BuyerMeeting',
                'tanggal_meeting' => $request->tanggal,
                'jam_meeting' => $request->jam,
                'tanggal_diupdate' => now()
            ]);

        // Ambil data klien untuk WhatsApp
        $client = DB::table('account')
            ->join('property', 'property.id_listing', '=', 'property_interests.id_listing')
            ->join('property_interests', function($join) use ($request) {
                $join->on('property_interests.id_klien', '=', 'account.id_account')
                     ->where('property_interests.id_listing', '=', $request->id_listing);
            })
            ->where('account.id_account', $request->id_account)
            ->select('account.nama as nama_klien', 'account.nomor_telepon', 'property.alamat as alamat_property')
            ->first();

        return response()->json([
            'success' => true,
            'nama_klien' => $client->nama_klien,
            'nomor_telepon' => $client->nomor_telepon,
            'alamat_property' => $client->alamat_property
        ]);
    }

    public function agentclosing(Request $request)
    {
        try {
            DB::beginTransaction();

            // ✅ Bersihkan angka ribuan
            $hargaDeal = (int) str_replace('.', '', $request->harga_deal);
            $hargaBidding = (int) str_replace('.', '', $request->harga_bidding);

            // ✅ Validasi input manual + cek foreign key exist
            $request->validate([
                'id_agent'   => 'required|string|exists:agent,id_agent',
                'id_klien'   => 'required|string|exists:account,id_account',
                'id_listing' => 'required|integer|exists:property,id_listing',
            ]);

            if ($hargaBidding < 1) {
                return back()->withErrors(['harga_bidding' => 'Harga bidding harus lebih besar dari 0.']);
            }

            if ($hargaBidding > $hargaDeal) {
                return back()->withErrors(['harga_bidding' => 'Harga bidding tidak boleh lebih besar dari harga deal.']);
            }

            // ✅ Ambil id_account dari agent untuk FK di transaction_details
            $agentAccount = DB::table('agent')
                ->where('id_agent', $request->id_agent)
                ->value('id_account');

            if (!$agentAccount) {
                throw new \Exception("Agent tidak memiliki id_account yang valid.");
            }

            // ✅ Generate ID transaksi unik (contoh: TRX001, TRX002)
            $lastTransaction = DB::table('transaction')->latest('id_transaction')->first();
            $newIdNumber = $lastTransaction
                ? str_pad((int)substr($lastTransaction->id_transaction, 3) + 1, 3, '0', STR_PAD_LEFT)
                : '001';
            $idTransaction = 'TRX' . $newIdNumber;

            // ✅ Hitung selisih & komisi agent
            $selisih = $hargaDeal - $hargaBidding;
            $komisiAgent = floor($selisih * 0.4);

            // ✅ Insert ke tabel transaction
            DB::table('transaction')->insert([
                'id_transaction'     => $idTransaction,
                'id_agent'           => $request->id_agent,
                'id_klien'           => $request->id_klien,
                'id_listing'         => $request->id_listing,
                'harga_deal'         => $hargaDeal,
                'harga_bidding'      => $hargaBidding,
                'selisih'            => $selisih,
                'komisi_agent'       => $komisiAgent,
                'status_transaksi'   => 'Closing',
                'tanggal_transaksi'  => now()->toDateString(),
                'tanggal_dibuat'     => now(),
                'tanggal_diupdate'   => now(),
            ]);

            // ✅ Insert ke tabel transaction_details
            DB::table('transaction_details')->insert([
                'id_account'         => $agentAccount, // 👈 FK ke account
                'id_transaction'     => $idTransaction,
                'status_transaksi'   => 'Closing',
                'catatan'            => 'Transaksi berhasil dibuat oleh agent.',
                'tanggal_dibuat'     => now(),
                'tanggal_diupdate'   => now(),
            ]);

            // ✅ Update status property_interests jadi "Closing"
            DB::table('property_interests')
                ->where('id_listing', $request->id_listing)
                ->where('id_klien', $request->id_klien)
                ->update([
                    'status' => 'Closing',
                    'tanggal_diupdate' => now(),
                ]);

            // ✅ Update status property jadi "Terjual"
            DB::table('property')
            ->where('id_listing', $request->id_listing)
            ->update([
                'status'      => 'Terjual',
                'tanggal_diupdate' => now(),
            ]);

            // update jumlah penjualan agent + 1
            DB::table('agent')
                ->where('id_agent', $agentAccount)
                ->update([
                    'jumlah_penjualan' => DB::raw('jumlah_penjualan + 1')
            ]);

            DB::commit();

            return redirect()->route('dashboard.agent')
                ->with('success', '✅ Transaksi berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Debug error biar jelas
            return back()->withErrors(['error' => '❌ Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }

    public function trackFinalStatus(Request $request)
    {
        DB::table('property_interests')
            ->where('id_account', $request->id_account)
            ->where('id_listing', $request->id_listing)
            ->update(['status' => $request->final_status, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
{
    try {
        $id_account = $request->id_account;
        $id_listing = $request->id_listing;
        $status = $request->status;

        // Update status di property_interests
        DB::table('property_interests')
            ->where('id_klien', $id_account) // sesuai schema kamu pakai id_klien
            ->where('id_listing', $id_listing)
            ->update([
                'status' => $status,
                'tanggal_diupdate' => now() // ✅ fix kolom timestamp
            ]);

        // Jika status = closing, simpan ke transaction dan company_earnings
        if (strtolower($status) === 'closing') {
            $harga_deal = (int) preg_replace('/[^\d]/', '', $request->harga_deal ?? 0);
            $harga_bidding = (int) preg_replace('/[^\d]/', '', $request->harga_bidding ?? 0);
            $selisih = $harga_deal - $harga_bidding;
            $komisi_agent = $selisih * 0.4;
            $pendapatan_bersih = $selisih * 0.6;

            DB::table('company_earnings')->insert([
                'id_listing' => $id_listing,
                'id_account' => session('id_account'),
                'tanggal' => now(),
                'harga_deal' => $harga_deal,
                'harga_bidding' => $harga_bidding,
                'selisih' => $selisih,
                'komisi_agent' => $komisi_agent,
                'pendapatan_bersih' => $pendapatan_bersih,
                'deskripsi' => 'Pendapatan dari transaksi properti'
            ]);

            DB::table('property')
                ->where('id_listing', $id_listing)
                ->update(['status' => 'sold']);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        \Log::error('updateStatus error: '.$e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateStatusClosing(Request $request)
{
    try {
        $id_account = $request->id_account;
        $id_listing = $request->id_listing;
        $status = $request->status;

        // ✅ Update status di tabel transaction
        $updatedTransaction = DB::table('transaction')
            ->where('id_klien', $id_account)
            ->where('id_listing', $id_listing)
            ->update([
                'status_transaksi' => $status,
                'tanggal_diupdate' => now()
            ]);
        \Log::info("Transaction updated rows: $updatedTransaction");

        // ✅ Ambil id_transaction
        $idTransaction = DB::table('transaction')
            ->where('id_klien', $id_account)
            ->where('id_listing', $id_listing)
            ->value('id_transaction');

        if (!$idTransaction) {
            throw new \Exception("id_transaction tidak ditemukan");
        }

        // ✅ Ambil semua id_account di transaction_details
        $transactionDetailAccounts = DB::table('transaction_details')
            ->where('id_transaction', $idTransaction)
            ->pluck('id_account');

        \Log::info("Transaction detail accounts: " . json_encode($transactionDetailAccounts));

        // ✅ Update status di transaction_details
        $updatedDetails = DB::table('transaction_details')
            ->where('id_transaction', $idTransaction)
            ->whereIn('id_account', $transactionDetailAccounts)
            ->update([
                'status_transaksi' => $status,
                'tanggal_diupdate' => now()
            ]);

        \Log::info("Transaction Details updated rows: $updatedDetails");

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        \Log::error('updateStatusClosing error: '.$e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}




    public function simpanEarning(Request $request)
    {
        // Ambil id_account agent dari session
        // Ambil id_account klien dari property_interests
        $interest = DB::table('property_interests')
            ->where('id_account', $request->id_account) // Klien
            ->latest('id_interest')
            ->first();

        if (!$interest) {
            return response()->json(['error' => 'Tidak ada data ketertarikan properti untuk user ini.'], 404);
        }

        // Ambil id_listing dari property_interests
        $id_listing = $interest->id_listing;

        // Ambil lokasi properti
        $lokasi = DB::table('property')
            ->where('id_listing', $id_listing)
            ->value('lokasi') ?? 'lokasi tidak diketahui';

        // Hitung selisih, komisi, dan pendapatan
        $harga_deal = (int) str_replace('.', '', $request->harga_deal);
        $harga_bidding = (int) str_replace('.', '', $request->harga_bidding);
        $selisih = $harga_deal - $harga_bidding;
        $komisi_agent = $selisih * 0.4;
        $pendapatan_bersih = $selisih * 0.6;

        // Insert ke tabel company_earnings (masukkan id_account klien, bukan id_account agent)
        DB::insert("INSERT INTO company_earnings
            (id_transaction, id_account, tanggal, harga_deal, harga_bidding, selisih, komisi_agent, pendapatan_bersih, deskripsi)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                0, // id_transaction kosong karena kamu gak punya tabel transaction
                session('id_account'), // id_account klien
                now()->toDateString(),
                $harga_deal,
                $harga_bidding,
                $selisih,
                $komisi_agent,
                $pendapatan_bersih,
                "Pendapatan dari transaksi ($lokasi)"
            ]);

        return response()->json(['success' => true]);
    }


    // public function getNewClientsJson()
    // {
    //     $idAccount = session('id_account');

    //     $newClients = DB::table('property_interests')
    //         ->join('account', 'property_interests.id_account', '=', 'account.id_account')
    //         ->join('property', 'property_interests.id_listing', '=', 'property.id_listing')
    //         ->where('property.id_agent', $idAccount)
    //         ->where('property_interests.is_hidden', 0)
    //         ->whereNull('property_interests.status') // anggap yang baru adalah yang belum difollowup
    //         ->select(
    //             'account.id_account as id',
    //             'account.nama',
    //             'property_interests.created_at'
    //         )
    //         ->orderByDesc('property_interests.created_at')
    //         ->get();

    //     $clients = $newClients->map(function ($client) {
    //         return [
    //             'id' => $client->id,
    //             'nama' => $client->nama,
    //             'created_at' => \Carbon\Carbon::parse($client->created_at)->diffForHumans(),
    //         ];
    //     });

    //     return response()->json([
    //         'count' => count($clients),
    //         'clients' => $clients,
    //     ]);
    // }


    public function verifyAgent($id_account)
    {
        // Update role menjadi 'Agent'
        DB::table('account')
            ->where('id_account', $id_account)
            ->update(['roles' => 'Agent']);

        // Update status di tabel agent menjadi 'Aktif'
        DB::table('agent')
            ->where('id_account', $id_account)
            ->update(['status' => 'Aktif']);

        // Ambil nomor telepon untuk redirect
        $account = DB::table('account')->where('id_account', $id_account)->first();
        $waNumber = '62' . ltrim($account->nomor_telepon, '0');

        $message = urlencode('Selamat! Akun Anda telah resmi menjadi agent kami.');

        return redirect()->away("https://wa.me/{$waNumber}?text={$message}");
    }

    public function rejectAgent($id_account)
    {
        // Update status di tabel agent menjadi 'Aktif'
        DB::table('agent')
            ->where('id_account', $id_account)
            ->update(['status' => 'Ditolak']);

        // Ambil nomor telepon untuk redirect
        $account = DB::table('account')->where('id_account', $id_account)->first();
        $waNumber = '62' . ltrim($account->nomor_telepon, '0');

        $message = urlencode('Mohon Maaf! Akun Anda tidak memenuhi kriteria kami, dikarenakan');

        return redirect()->away("https://wa.me/{$waNumber}?text={$message}");
    }

    public function verifyClient($id_account)
    {
        DB::table('informasi_klien')
            ->where('id_account', $id_account)
            ->update(['status_verifikasi' => 'Terverifikasi']);

        // Ambil nomor telepon untuk redirect
        $account = DB::table('account')->where('id_account', $id_account)->first();
        $waNumber = '62' . ltrim($account->nomor_telepon, '0');

        $message = urlencode('Selamat! Akun Anda telah terverifikasi.');

        return redirect()->away("https://wa.me/{$waNumber}?text={$message}");
    }

    public function rejectClient($id_account)
    {
        DB::table('informasi_klien')
            ->where('id_account', $id_account)
            ->update(['status_verifikasi' => 'Ditolak']);

        // Ambil nomor telepon untuk redirect
        $account = DB::table('account')->where('id_account', $id_account)->first();
        $waNumber = '62' . ltrim($account->nomor_telepon, '0');

        $message = urlencode('Maaf, verifikasi akun Anda ditolak.');

        return redirect()->away("https://wa.me/{$waNumber}?text={$message}");
    }

    public function dashboardDetail($id_listing, $id_account)
    {
        $property = DB::table('property')->where('id_listing', $id_listing)->first();

        if (!$property) {
            return redirect()->route('dashboard.owner')->with('error', 'Properti tidak ditemukan.');
        }

        $client = null;
        $statusTransaksi = null;
        $progressType = null;

        $transaction = Transaction::where('id_listing', $id_listing)
            ->where('id_klien', $id_account)
            ->first();

        if ($transaction) {
            $statusTransaksi = $transaction->status_transaksi;

            if (in_array($statusTransaksi, ['Closing', 'Kuitansi', 'Kode Billing', 'Kutipan Risalah Lelang', 'Akte Grosse'])) {
                $progressType = 'register';
            } elseif (in_array($statusTransaksi, ['Balik Nama', 'Eksekusi Pengosongan', 'Selesai'])) {
                $progressType = 'pengosongan';
            }

            // Ambil catatan dari transaction_details
            $transactionNotes = DB::table('transaction_details')
                ->join('account', 'transaction_details.id_account', '=', 'account.id_account')
                ->where('transaction_details.id_transaction', $transaction->id_transaction)
                ->orderByDesc('transaction_details.tanggal_dibuat')
                ->select(
                    'transaction_details.*',
                    'account.nama as account_name'
                )
                ->get();
        } else {
            $propertyInterest = PropertyInterest::where('id_listing', $id_listing)
                ->where('id_klien', $id_account)
                ->first();

            if ($propertyInterest) {
                $statusTransaksi = $propertyInterest->status;
                $progressType = 'agent';
            }

            $transactionNotes = collect(); // atau []
        }


        // Ambil data klien
        if ($id_account) {
            $account = Account::where('id_account', $id_account)->first();
            $informasi = InformasiKlien::where('id_account', $id_account)->first();

            $client = (object)[
                'id_account'    => $id_account,
                'nama'          => $account->nama ?? '-',
                'nomor_telepon' => $account->nomor_telepon ?? '-',
                'gambar_ktp'    => $informasi->gambar_ktp ?? null,
                'gambar_npwp'   => $informasi->gambar_npwp ?? null,
            ];
        }

        return view('Agent.detail', [
            'property'         => $property,
            'client'           => $client,
            'statusTransaksi'  => $statusTransaksi,
            'progressType'     => $progressType,
            'transactionNotes' => $transactionNotes,
        ]);
    }

    public function updateOwner(Request $request, $id_listing, $id_account)
    {
        $request->validate([
            'status' => 'required|string',
            'buyer_meeting_datetime' => 'nullable|date',
        ]);

        $status = $request->status;

        // ✅ Kalau Closing → lakukan proses tambahan
        if ($status === 'Closing') {
            try {
                DB::beginTransaction();

                // Ambil data property
                $property = DB::table('property')->where('id_listing', $id_listing)->first();
                if (!$property) {
                    throw new \Exception('Property tidak ditemukan.');
                }

                // Ambil data agent dari property
                $idAgent = $property->id_agent;
                $hargaDeal = (int) $property->harga;

                // Ambil id_account agent yang login
                $idAccountAgent = session('id_account') ?? Cookie::get('id_account');
                $account = Account::find($idAccountAgent);

                // Harga bidding dari request
                $hargaBidding = (int) str_replace('.', '', $request->harga_bidding);
                if ($hargaBidding < 1) {
                    return back()->withErrors(['harga_bidding' => 'Harga bidding harus lebih besar dari 0.']);
                }
                if ($hargaBidding > $hargaDeal) {
                    return back()->withErrors(['harga_bidding' => 'Harga bidding tidak boleh lebih besar dari harga deal.']);
                }

                // Generate ID transaksi unik (TRX001, TRX002)
                $lastTransaction = DB::table('transaction')->latest('id_transaction')->first();
                $newIdNumber = $lastTransaction
                    ? str_pad((int)substr($lastTransaction->id_transaction, 3) + 1, 3, '0', STR_PAD_LEFT)
                    : '001';
                $idTransaction = 'TRX' . $newIdNumber;

                // Hitung selisih & komisi agent
                $selisih = $hargaDeal - $hargaBidding;
                $komisiAgent = floor($selisih * 0.4);

                // Insert ke tabel transaction
                DB::table('transaction')->insert([
                    'id_transaction'     => $idTransaction,
                    'id_agent'           => $idAgent,
                    'id_klien'           => $id_account,
                    'id_listing'         => $id_listing,
                    'harga_deal'         => $hargaDeal,
                    'harga_bidding'      => $hargaBidding,
                    'selisih'            => $selisih,
                    'komisi_agent'       => $komisiAgent,
                    'status_transaksi'   => 'Closing',
                    'tanggal_transaksi'  => now()->toDateString(),
                    'tanggal_dibuat'     => now(),
                    'tanggal_diupdate'   => now(),
                ]);

                // Insert ke transaction_details
                DB::table('transaction_details')->insert([
                    'id_account'         => $idAccountAgent,
                    'id_transaction'     => $idTransaction,
                    'status_transaksi'   => 'Closing',
                    'catatan'            => 'Transaksi berhasil dibuat.',
                    'tanggal_dibuat'     => now(),
                    'tanggal_diupdate'   => now(),
                ]);

                // Update status di property_interests
                DB::table('property_interests')
                    ->where('id_listing', $id_listing)
                    ->where('id_klien', $id_account)
                    ->update([
                        'status'            => 'Closing',
                        'tanggal_diupdate'  => now(),
                    ]);

                // Update status di property
                DB::table('property')
                    ->where('id_listing', $id_listing)
                    ->update([
                        'status'            => 'Terjual',
                        'tanggal_diupdate'  => now(),
                    ]);

                // Tambah jumlah penjualan agent
                DB::table('agent')
                    ->where('id_agent', $idAgent)
                    ->update([
                        'jumlah_penjualan' => DB::raw('jumlah_penjualan + 1'),
                    ]);

                DB::commit();

                if ($account && $account->roles === 'Owner') {
                    return redirect()->route('dashboard.owner')->with('success', 'Transaksi Closing berhasil disimpan.');
                } else {
                    return redirect()->route('dashboard.agent')->with('success', 'Status berhasil diperbarui.');
                }

            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withErrors(['error' => '❌ Gagal Closing: ' . $e->getMessage()]);
            }
        }

        // ✅ Kalau status lain → jalankan logika biasa
        if (in_array($status, ['Pending', 'FollowUp', 'BuyerMeeting', 'Gagal'])) {
            // Progress Agent → property_interests
            PropertyInterest::where('id_listing', $id_listing)
                ->where('id_klien', $id_account)
                ->update(['status' => $status]);

            // Kalau BuyerMeeting → update tanggal di property
            if ($status === 'BuyerMeeting' && $request->buyer_meeting_datetime) {
                DB::table('property')->where('id_listing', $id_listing)
                    ->update(['tanggal_buyer_meeting' => $request->buyer_meeting_datetime]);
            }

        } elseif (in_array($status, [
            'Kuitansi', 'Kode Billing', 'Kutipan Risalah Lelang',
            'Akte Grosse', 'Balik Nama', 'Eksekusi Pengosongan', 'Selesai'
        ])) {
            $transaction = Transaction::where('id_listing', $id_listing)
                ->where('id_klien', $id_account)
                ->first();

            $idAccountAgent = session('id_account') ?? Cookie::get('id_account');
            $account = Account::find($idAccountAgent);

            // Progress Register/Pengosongan → transaction
            DB::table('transaction')
            ->where('id_listing', $id_listing)
            ->where('id_klien', $id_account)
            ->update([
                'status_transaksi' => $status,
                'tanggal_diupdate' => now(),
            ]);

            DB::table('transaction_details')->insert([
                'id_account'         => $idAccountAgent,
                'id_transaction'     => $transaction->id_transaction,
                'status_transaksi'   => $status,
                'catatan'            => $request->input('comment'),
                'tanggal_dibuat'     => now(),
                'tanggal_diupdate'   => now(),
            ]);


        }

        if ($account && $account->roles === 'Owner') {
            return redirect()->route('dashboard.owner')->with('success', 'Transaksi Closing berhasil disimpan.');
        } else {
            return redirect()->route('dashboard.agent')->with('success', 'Status berhasil diperbarui.');
        }
    }



    public function scrape(Request $request)
{
    $tipe = $request->input('tipe');

    try {
        Artisan::call('app:scrape-property', [
            'kategori' => $tipe
        ]);

        return response()->json(['message' => 'Scrape berhasil dijalankan.']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal menjalankan scrape: ' . $e->getMessage()
        ], 500);
    }
}

public function exportByType($tipe)
{
    $filename = 'property_' . strtolower($tipe) . '_' . now()->format('Ymd_His') . '.csv';

    $properties = Property::whereRaw('LOWER(tipe) = ?', [strtolower($tipe)])->get();

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = [
        'id_listing',
        'id_agent',
        'vendor',
        'judul',
        'deskripsi',
        'tipe',
        'harga',
        'lokasi',
        'luas',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'sertifikat',
        'status',
        'gambar',
        'payment',
        'uang_jaminan',
        'batas_akhir_jaminan',
        'batas_akhir_penawaran',
        'tanggal_buyer_meeting',
        'tanggal_dibuat',
        'tanggal_diupdate',
    ];

    $callback = function () use ($properties, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns); // Header

        foreach ($properties as $prop) {
            $row = [];
            foreach ($columns as $col) {
                $value = $prop->$col;

                // Format tanggal ke format manusia jika perlu
                if ($value instanceof \Carbon\Carbon) {
                    $value = $value->format('Y-m-d');
                }

                $row[] = $value;
            }
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function exportList(Request $request)
{
    $driver = DB::getDriverName();              // 'pgsql', 'mysql', 'sqlite', ...
    $cast   = $driver === 'pgsql' ? 'TEXT' : 'CHAR';

    $dbg = [
        'driver'        => $driver,
        'cast'          => $cast,
        'search_raw'    => $request->get('search'),
        'property_type' => $request->get('property_type'),
        'province'      => $request->get('province'),
        'city'          => $request->get('city'),
        'district'      => $request->get('district'),
        'page'          => (int)($request->get('page') ?? 1),
    ];

    $q = Property::from('property as p')
        ->select([
            'p.id_listing','p.lokasi','p.tipe','p.luas','p.harga','p.gambar',
            'p.sertifikat','p.id_agent','p.link',
            'p.exported', // <<< WAJIB: untuk highlight di tabel
            DB::raw("('https://solusindolelang.com/property-detail/' || p.id_listing || '/' || COALESCE(p.id_agent, '')) as link_solusindo"),
        ]);

    // FILTER SEARCH numerik
    $search = trim((string) $request->get('search', ''));
    $dbg['search_trimmed'] = $search;

    if ($search !== '') {
        if (preg_match('/^\d+$/', $search)) {
            $q->where('p.id_listing', (int)$search);
            $dbg['search_mode']    = 'exact_numeric';
            $dbg['search_binding'] = (int)$search;
        } else {
            $q->whereRaw('1=0');
            $dbg['search_mode'] = 'blocked_non_numeric';
        }
    } else {
        $dbg['search_mode'] = 'empty';
    }

    // FILTER lain
    if ($request->filled('property_type')) {
        $q->whereRaw('LOWER(p.tipe)=?', [strtolower($request->get('property_type'))]);
    }
    if ($request->filled('province') && $request->get('province') !== 'Pilih Provinsi') {
        $q->where('p.provinsi', $request->get('province'));
    }
    if ($request->filled('city') && $request->get('city') !== 'Pilih Kota/Kab') {
        $q->where('p.kota', $request->get('city'));
    }
    if ($request->filled('district') && $request->get('district') !== 'Pilih Kecamatan') {
        $q->where('p.kecamatan', $request->get('district'));
    }

    // Optional: kalau mau yang sudah diexport tetap ikut, tapi diurutkan belakangan/di depan
    // Contoh: yang belum diexport dulu baru yang sudah diexport
    // $q->orderBy('p.exported', 'asc')->orderBy('p.id_listing', 'asc');

    $dbg['sql']      = $q->toSql();
    $dbg['bindings'] = $q->getBindings();

    $dbg['count_total'] = (clone $q)->count();

    $page = max(1, (int) ($request->get('page') ?? 1));
    $exportProperties = $q->orderBy('p.id_listing', 'asc')
        ->paginate(15, ['*'], 'page', $page)
        ->appends(array_merge(
            $request->only(['search','property_type','province','city','district']),
            ['tab'=>'export']
        ));

    $dbg['count_page']   = $exportProperties->count();
    $dbg['current_page'] = $exportProperties->currentPage();
    $dbg['last_page']    = $exportProperties->lastPage();

    return view('partial.export_list', [
        'exportProperties' => $exportProperties,
        '___dbg'           => $dbg,
    ]);
}


public function stokerList(Request $request)
{
    // Sanitasi input
    $search   = trim((string) $request->get('search', ''));
    $vendor   = trim((string) $request->get('vendor', ''));   // input vendor (free text)
    $ptype    = $request->get('property_type');
    $province = $request->get('province');
    $city     = $request->get('city');
    $district = $request->get('district');

    // Placeholder yang kudu di-skip
    $skipValues = ['Pilih Provinsi', 'Pilih Kota/Kab', 'Pilih Kota/Kabupaten', 'Pilih Kecamatan', ''];

    $q = \App\Models\Property::from('property as p')
        ->select(
            'p.id_listing',
            'p.lokasi',
            'p.luas',
            'p.harga',
            'p.gambar',
            'p.status',
            'p.tipe',
            'p.provinsi',
            'p.kota',
            'p.kecamatan',
            'p.vendor'
        )
        // status dibikin tahan banting: lower+trim
        ->whereRaw('LOWER(TRIM(p.status)) = ?', ['tersedia'])

        // SEARCH ID LISTING: hanya angka; selain itu abaikan (jangan bunuh hasil)
        ->when($search !== '', function ($query) use ($search) {
            if (ctype_digit($search)) {
                return $query->where('p.id_listing', (int) $search);
            }
            // kalau bukan angka, jangan kasih 1=0; cukup diabaikan
            return $query;
        })

        // FILTER VENDOR: multi-keyword, order-agnostic, case-insensitive + alias akronim
        ->when($vendor !== '', function ($q) use ($vendor) {
            $tokens = array_filter(preg_split('/\s+/', strtolower($vendor)));

            // peta alias akronim -> nama panjang yang sering dipakai
            $alias = [
                'bri'      => 'bank rakyat indonesia',
                'bni'      => 'bank negara indonesia',
                'btn'      => 'bank tabungan negara',
                'mandiri'  => 'bank mandiri',
                'bca'      => 'bank central asia',
                'mega'     => 'bank mega',
                'bsi'      => 'bank syariah indonesia',
            ];

            $q->where(function($qq) use ($tokens, $alias) {
                foreach ($tokens as $t) {
                    $qq->where(function($w) use ($t, $alias) {
                        // match langsung kata kunci
                        $w->whereRaw('LOWER(TRIM(p.vendor)) LIKE ?', ['%'.$t.'%']);

                        // kalau ada aliasnya, OR-kan dengan nama panjang
                        if (isset($alias[$t])) {
                            $w->orWhereRaw('LOWER(TRIM(p.vendor)) LIKE ?', ['%'.$alias[$t].'%']);
                        }
                    });
                }
            });
        })

        // FILTER tipe
        ->when($ptype, fn($q,$v) => $q->whereRaw('LOWER(p.tipe)=?', [strtolower($v)]))

        // FILTER lokasi: pakai variabel yang sudah disanitasi
        ->when(!in_array((string)$province, $skipValues, true), fn($q)       => $q->where('p.provinsi',  $province))
        ->when(!in_array((string)$city,     $skipValues, true), fn($q)       => $q->where('p.kota',      $city))
        ->when(!in_array((string)$district, $skipValues, true), fn($q)       => $q->where('p.kecamatan', $district))

        ->orderByDesc('p.id_listing');

    // Pagination
    $page = max(1, (int) ($request->get('page') ?? 1));
    $stokerProperties = $q->paginate(10, ['*'], 'page', $page)
        ->appends(array_merge(
            $request->only(['search','vendor','property_type','province','city','district']),
            ['tab' => 'stoker']
        ));

    // Balikin partial (AJAX fragment)
    return view('partial.stoker_list', compact('stokerProperties'));
}




public function stokerBulkSold(Request $request)
{
    $ids = collect(explode(',', (string)$request->input('selected_ids')))
        ->filter(fn($v) => ctype_digit($v))
        ->map(fn($v) => (int)$v)
        ->unique()
        ->values();

    if ($ids->isEmpty()) {
        return back()->with('error', 'Tidak ada listing yang dipilih.');
    }

    \DB::table('property')
        ->whereIn('id_listing', $ids)
        ->update([
            'status'            => 'Terjual',
            'tanggal_diupdate'  => now(),
        ]);

    // Ambil tab yang diminta, default 'stoker'
    $tab = $request->input('return_tab', 'stoker');

    // Tentukan route berdasarkan role
    $route = session('role') === 'Owner' ? 'dashboard.owner' : 'dashboard.agent';

    // Redirect dengan query ?tab=stoker + fragment #stoker
    return redirect()
        ->route($route, ['tab' => $tab])
        ->withFragment($tab)
        ->with('stoker_clear_selection', true)
        ->with('success', 'Berhasil menandai '.count($ids).' listing sebagai Terjual.');
}

public function transaksiList(Request $request)
{
    // ==== SANITASI INPUT (mirror stokerList) ====
    $search   = trim((string) $request->get('search', ''));
    $vendor   = trim((string) $request->get('vendor', ''));
    $ptype    = $request->get('property_type');
    $province = $request->get('province');
    $city     = $request->get('city');
    $district = $request->get('district');

    // Placeholder yang kudu di-skip (SAMA persis dengan stokerList)
    $skipValues = ['Pilih Provinsi', 'Pilih Kota/Kab', 'Pilih Kota/Kabupaten', 'Pilih Kecamatan', ''];

    // ==== QUERY MIRROR STOKER, + JOIN KE AGENT UNTUK CO PIC ====
    $q = Property::from('property as p')
        ->leftJoin('agent as ag', function($join) {
            // JOIN pakai TRIM + UPPER biar aman kalau ada spasi / beda kapital
            $join->on(
                DB::raw('UPPER(TRIM(ag.id_agent))'),
                '=',
                DB::raw('UPPER(TRIM(p.id_agent))')
            );
        })
        ->select(
            'p.id_listing',
            'p.lokasi',
            'p.luas',
            'p.harga',              // harga markup (web)
            'p.gambar',
            'p.status',             // dipakai untuk badge Status / Closing
            'p.tipe',
            'p.provinsi',
            'p.kota',
            'p.kecamatan',
            'p.vendor',
            'p.tanggal_diupdate',
            'p.id_agent',           // ikut diseleksi buat debugging
            DB::raw("COALESCE(ag.nama, '') as agent_nama"),
            DB::raw('NULL::integer as id_transaksi')
        )

        // SEARCH ID LISTING: hanya angka; selain itu diabaikan (jangan 1=0)
        ->when($search !== '', function ($query) use ($search) {
            if (ctype_digit($search)) {
                return $query->where('p.id_listing', (int) $search);
            }
            return $query;
        })

        // FILTER VENDOR: multi-keyword + alias (mirror stokerList)
        ->when($vendor !== '', function ($q) use ($vendor) {
            $tokens = array_filter(preg_split('/\s+/', strtolower($vendor)));

            $alias = [
                'bri'      => 'bank rakyat indonesia',
                'bni'      => 'bank negara indonesia',
                'btn'      => 'bank tabungan negara',
                'mandiri'  => 'bank mandiri',
                'bca'      => 'bank central asia',
                'mega'     => 'bank mega',
                'bsi'      => 'bank syariah indonesia',
            ];

            $q->where(function($qq) use ($tokens, $alias) {
                foreach ($tokens as $t) {
                    $qq->where(function($w) use ($t, $alias) {
                        $w->whereRaw('LOWER(TRIM(p.vendor)) LIKE ?', ['%'.$t.'%']);

                        if (isset($alias[$t])) {
                            $w->orWhereRaw('LOWER(TRIM(p.vendor)) LIKE ?', ['%'.$alias[$t].'%']);
                        }
                    });
                }
            });
        })

        // FILTER tipe
        ->when($ptype, fn($q,$v) => $q->whereRaw('LOWER(p.tipe)=?', [strtolower($v)]))

        // FILTER lokasi: SKIP placeholder kayak "Pilih Provinsi"
        ->when(!in_array((string)$province, $skipValues, true), fn($q)       => $q->where('p.provinsi',  $province))
        ->when(!in_array((string)$city,     $skipValues, true), fn($q)       => $q->where('p.kota',      $city))
        ->when(!in_array((string)$district, $skipValues, true), fn($q)       => $q->where('p.kecamatan', $district))

        ->orderByDesc('p.id_listing');

    // ==== PAGINATION (mirror stokerList) ====
    $page = max(1, (int) ($request->get('page') ?? 1));
    $transaksiProperties = $q->paginate(10, ['*'], 'page', $page)
        ->appends(array_merge(
            $request->only(['search','vendor','property_type','province','city','district']),
            ['tab' => 'transaksi']
        ));

    // Balikin partial (AJAX fragment) – sama seperti stokerList
    return view('partial.transaksi_list', compact('transaksiProperties'));
}

public function transaksiPropertyHistory(Request $request): View
{
    $idListing = (int) $request->get('id_listing');

    /** @var \App\Models\Property|null $current */
    $current = \App\Models\Property::find($idListing);
    if (!$current) {
        abort(404);
    }

    // --- Normalisasi sertifikat: huruf + angka saja, lowercase ---
    $sertKey = null;
    if (!empty($current->sertifikat)) {
        $lower  = mb_strtolower($current->sertifikat, 'UTF-8');
        // buang semua karakter non huruf/angka
        $sertKey = preg_replace('/[^a-z0-9]/u', '', $lower);
    }

    // --- Ambil semua listing dengan aset yang sama ---
    $history = \App\Models\Property::query()
        ->where('id_listing', '!=', $current->id_listing)

        // sama-sama sertifikat (normalisasi)
        ->when($sertKey, function ($q) use ($sertKey) {
            $q->whereRaw(
                "regexp_replace(lower(coalesce(sertifikat, '')), '[^a-z0-9]', '', 'g') = ?",
                [$sertKey]
            );
        })

        // luas sama
        ->when($current->luas, function ($q) use ($current) {
            $q->where('luas', $current->luas);
        })

        // kota sama
        ->when($current->kota, function ($q) use ($current) {
            $q->whereRaw('LOWER(TRIM(kota)) = ?', [strtolower(trim($current->kota))]);
        })

        // urutkan berdasarkan tanggal lelang (paling awal = lelang ke-1)
        ->orderByRaw('COALESCE(batas_akhir_penawaran, tanggal_buyer_meeting, tanggal_dibuat) ASC')
        ->get();

    // gabungkan current + history, lalu urutkan lagi supaya pasti berurutan
    $rows = collect([$current])
        ->merge($history)
        ->sortBy(function ($row) {
            return $row->batas_akhir_penawaran
                ?? $row->tanggal_buyer_meeting
                ?? $row->tanggal_dibuat;
        })
        ->values();

    // 🔥 ambil nama CO PIC (agent) berdasarkan id_agent di semua row history
    $agentNames = DB::table('agent')
        ->whereIn('id_agent', $rows->pluck('id_agent')->filter()->unique())
        ->pluck('nama', 'id_agent'); // hasil: [id_agent => 'Nama Agent']

    return view('partial.transaksi_property_history', [
        'rows'       => $rows,
        'current'    => $current,
        'agentNames' => $agentNames,
    ]);
}

public function updatetransaksi(Request $request)
{
    // Validasi basic
    $data = $request->validate([
        'id_listing'       => 'required|integer|exists:property,id_listing',
        'closing_type'     => 'required|in:profit,price_gap', // 'profit' / 'price_gap'
        'id_agent'         => 'nullable|string',
        'id_klien'         => 'nullable|string',
        'harga_menang'     => 'required|string',   // masih format "4.000.000.000"
        'komisi_persen'    => 'nullable|numeric',  // contoh: 5, 10
        'status'           => 'nullable|string',
        'tanggal_diupdate' => 'required|date',     // tanggal closing (Y-m-d)
        'biaya_balik_nama' => 'nullable|string',  // dari input modal (boleh ada titik)
        'biaya_eksekusi'   => 'nullable|string',  // dari input modal (boleh ada titik)
    ]);

    // --- Ambil property untuk dapat harga_limit ---
    /** @var \App\Models\Property $property */
    $property = Property::findOrFail($data['id_listing']);

    $hargaMarkup = (float) ($property->harga ?? 0);
    $hargaLimit  = $hargaMarkup > 0 ? round($hargaMarkup / 1.278) : 0;

    // --- Normalisasi angka dari input harga_menang (hapus titik/koma dll) ---
    $hargaBidding = (int) preg_replace('/[^\d]/', '', $data['harga_menang'] ?? '');
    if ($hargaBidding <= 0) {
        return back()->with('error', 'Harga menang tidak valid.')->withInput();
    }

    // --- Hitung selisih ---
    $selisih = max($hargaBidding - $hargaLimit, 0);

    // --- Hitung kenaikan_dari_limit (persentase) ---
    $kenaikanDariLimit = 0;
    if ($hargaLimit > 0 && $selisih > 0) {
        $kenaikanDariLimit = round(($selisih / $hargaLimit) * 100, 2); // contoh: 15.25 (%)
    }

    // --- Normalisasi biaya balik nama & biaya pengosongan (eksekusi) ---
    $biayaBalikNama   = (int) preg_replace('/[^\d]/', '', $data['biaya_balik_nama'] ?? '');
    $biayaPengosongan = (int) preg_replace('/[^\d]/', '', $data['biaya_eksekusi'] ?? '');

    // --- Map skema_komisi (label) dari closing_type ---
    $closingType  = $data['closing_type']; // 'profit' / 'price_gap'
    $skemaKomisi  = $closingType === 'price_gap'
        ? 'Selisih harga'
        : 'Persentase komisi';

    // --- Persentase komisi (dalam bentuk 0.05) ---
    $persentaseKomisi = null;
    if ($closingType === 'profit') {
        $rawPersen = $request->input('komisi_persen', 0);
        // support "5" atau "5,5"
        $rawPersen = str_replace(',', '.', (string) $rawPersen);
        $angkaPersen = (float) $rawPersen;
        $persentaseKomisi = $angkaPersen > 0 ? $angkaPersen / 100 : 0;
    }

    // --- Basis pendapatan ---
    if ($closingType === 'price_gap') {
        // basis = selisih harga
        $basisPendapatan = $selisih;
    } else {
        // basis = harga_bidding * persen
        $basisPendapatan = (int) round($hargaBidding * ($persentaseKomisi ?? 0));
    }

    // --- Ambil id_agent & id_klien ---
    // Dari form kita sudah kirim id_agent = kode AGxxx, jadi langsung pakai
    $idAgent = $data['id_agent'] ?: $property->id_agent;

    // Client: id_klien = id_account user (dropdown)
    $idKlien = $data['id_klien'] ?: null;

    // --- Status & tanggal transaksi ---
    $statusTransaksi   = $data['status'] ?: 'Closing';
    $tanggalTransaksi  = $data['tanggal_diupdate']; // sudah format Y-m-d

    // --- Generate ID transaksi kalau belum ada ---
    $idTransaksi = $request->input('id_transaksi');

    if (!$idTransaksi) {
        // Ambil id_transaction terbesar yang sudah ada, contoh: TR0007
        $lastId = Transaction::orderBy('id_transaction', 'desc')
            ->value('id_transaction');

        if (!$lastId) {
            $nextNumber = 1;
        } else {
            // buang huruf, ambil angka di belakang TR
            $num = (int) preg_replace('/\D/', '', $lastId);
            $nextNumber = $num + 1;
        }

        // Format: TR0001 (4 digit)
        $idTransaksi = 'TR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // --- Siapkan payload untuk insert/update transaksi utama ---
    $payload = [
        'id_transaction'      => $idTransaksi,
        'skema_komisi'        => $skemaKomisi,
        'id_agent'            => $idAgent,
        'id_klien'            => $idKlien,
        'id_listing'          => $property->id_listing,
        'harga_limit'         => $hargaLimit,
        'harga_bidding'       => $hargaBidding,
        'selisih'             => $selisih,
        'persentase_komisi'   => $persentaseKomisi,
        'basis_pendapatan'    => $basisPendapatan,
        'status_transaksi'    => $statusTransaksi,
        'tanggal_transaksi'   => $tanggalTransaksi,
        'kenaikan_dari_limit' => $kenaikanDariLimit, // dalam persen (misal 12.5)
        'biaya_baliknama'     => $biayaBalikNama,
        'biaya_pengosongan'   => $biayaPengosongan,
    ];

    // --- Insert baru kalau belum ada, kalau sudah ada → update ---
    $existing = Transaction::where('id_transaction', $idTransaksi)->first();

    if ($existing) {
        $existing->update($payload);
    } else {
        Transaction::create($payload);
    }

    /*
    |--------------------------------------------------------------------------
    |  SINKRONISASI DETAIL PEMBAGIAN KE TABEL transaction_commissions
    |--------------------------------------------------------------------------
    |  - Basis = $basisPendapatan (fee / selisih harga)
    |  - Skema pembagian mengikuti KONSTANTA yang sama seperti di JS
    |  - COPIC: dibagi rata ke semua agent yang PERNAH pegang aset yg sama
    |    (berdasarkan sertifikat + luas + kota), tanpa dobel kalau agent sama.
    */

    // Kalau basis 0 → hapus semua detail komisi & lompat ke update status property
    if ($basisPendapatan <= 0) {
        TransactionCommission::where('id_transaction', $idTransaksi)->delete();
    } else {

        // --- 1. Konstanta skema (harus sama dengan JS) ---
        $KOMISI_SCHEMA = [
            ['kode' => 'UP1',       'rate' => 0.004000],
            ['kode' => 'UP2',       'rate' => 0.003000],
            ['kode' => 'UP3',       'rate' => 0.002000],
            ['kode' => 'LISTER',    'rate' => 0.010000],
            ['kode' => 'COPIC',     'rate' => 0.002500],
            ['kode' => 'CONS',      'rate' => 0.008500],
            ['kode' => 'REWARD',    'rate' => 0.030000],
            ['kode' => 'INV_FUND',  'rate' => 0.020000],
            ['kode' => 'PROMO_FUND','rate' => 0.020000],
            ['kode' => 'PIC1',      'rate' => 0.040000],
            ['kode' => 'PIC2',      'rate' => 0.040000],
            ['kode' => 'PIC3',      'rate' => 0.040000],
            ['kode' => 'PIC4',      'rate' => 0.040000],
            ['kode' => 'PIC5',      'rate' => 0.040000],
            ['kode' => 'THC',       'rate' => 0.400000],
            ['kode' => 'SERVICE',   'rate' => 0.100000],
            ['kode' => 'PRINC_FEE', 'rate' => 0.030000],
            ['kode' => 'INV_SHARE', 'rate' => 0.095200],
            ['kode' => 'MGMT_FUND', 'rate' => 0.059500],
            ['kode' => 'EMP_INC',   'rate' => 0.015300],
        ];

        // mapping kode skema -> id_agent tetap (seperti di JS)
        $KOMISI_KODE_TO_AGENT_ID = [
            'LISTER'     => 'AG001',
            'CONS'       => 'AG014',
            'REWARD'     => 'AG006',
            'INV_FUND'   => 'AG006',
            'PROMO_FUND' => 'AG006',
            'PIC1'       => 'AG006',
            'PIC2'       => 'AG012',
            'PIC3'       => 'AG008',
            'PIC4'       => 'AG014',
            'PIC5'       => 'AG009',
            'SERVICE'    => 'AG006',
            'PRINC_FEE'  => 'AG012',
            'INV_SHARE'  => 'AG001',
            'MGMT_FUND'  => 'AG006',
            'EMP_INC'    => 'AG001',
        ];

        // --- 2. Hitung upline dari closing agent (sama seperti JS) ---
        $getUplineId = function (?string $agentId, ?string $defaultId = null) {
            if (!$agentId) {
                return $defaultId;
            }
            // NOTE: ganti 'upline_id' kalau nama kolom di tabel agent berbeda
            $up = DB::table('agent')
                ->where('id_agent', $agentId)
                ->value('upline_id');

            if ($up && trim($up) !== '') {
                return (string) $up;
            }
            return $defaultId;
        };

        $up1Id = $getUplineId($idAgent, 'AG006');    // default AG006 kalau tidak ada
        $up2Id = $getUplineId($up1Id, 'AG001');      // default AG001
        $up3Id = $getUplineId($up2Id, 'AG001');      // default AG001

        // --- 3. Cari daftar agent COPIC dari sejarah aset yang sama ---
        $sertKey = null;
        if (!empty($property->sertifikat)) {
            $lower   = mb_strtolower($property->sertifikat, 'UTF-8');
            $sertKey = preg_replace('/[^a-z0-9]/u', '', $lower);
        }

        $history = Property::query()
            ->where('id_listing', '!=', $property->id_listing)
            ->when($sertKey, function ($q) use ($sertKey) {
                $q->whereRaw(
                    "regexp_replace(lower(coalesce(sertifikat, '')), '[^a-z0-9]', '', 'g') = ?",
                    [$sertKey]
                );
            })
            ->when($property->luas, function ($q) use ($property) {
                $q->where('luas', $property->luas);
            })
            ->when($property->kota, function ($q) use ($property) {
                $q->whereRaw('LOWER(TRIM(kota)) = ?', [strtolower(trim($property->kota))]);
            })
            ->get();

        $rows = collect([$property])->merge($history);

        // Kumpulkan semua id_agent unik yang PERNAH pegang aset ini
        $copicAgentIds = $rows->pluck('id_agent')
            ->filter()
            ->unique()
            ->values()
            ->all();    // contoh: ['AG001','AG002']

        // --- 4. Susun map role -> daftar id_agent ---
        $roleAgentMap = [];

        // THC = agent closing
        if ($idAgent) {
            $roleAgentMap['THC'] = [$idAgent];
        }

        // Upline
        if ($up1Id) {
            $roleAgentMap['UP1'] = [$up1Id];
        }
        if ($up2Id) {
            $roleAgentMap['UP2'] = [$up2Id];
        }
        if ($up3Id) {
            $roleAgentMap['UP3'] = [$up3Id];
        }

        // Static mapping (LISTER, CONS, PIC1.., office funds, dll)
        foreach ($KOMISI_KODE_TO_AGENT_ID as $kode => $agentStaticId) {
            if ($agentStaticId) {
                $roleAgentMap[$kode] = [$agentStaticId];
            }
        }

        // COPIC: semua agent unik dari aset yang sama
        if (!empty($copicAgentIds)) {
            $roleAgentMap['COPIC'] = $copicAgentIds;
        }

        // Pastikan tiap list unik & tidak kosong
        foreach ($roleAgentMap as $kode => $ids) {
            $ids = array_filter($ids);
            $ids = array_values(array_unique($ids));
            if (empty($ids)) {
                unset($roleAgentMap[$kode]);
            } else {
                $roleAgentMap[$kode] = $ids;
            }
        }

        // --- 5. Sinkronisasi ke transaction_commissions (tanpa reset ID) ---
        $keptIds = []; // id row yang dipertahankan / diupdate

        foreach ($KOMISI_SCHEMA as $item) {
            $kode = $item['kode'];
            $rate = (float) $item['rate'];

            // Kalau tidak ada agent untuk kode ini, skip
            if (!isset($roleAgentMap[$kode]) || empty($roleAgentMap[$kode])) {
                continue;
            }

            $agents = $roleAgentMap[$kode];

            if ($kode === 'COPIC') {
                // COPIC 0.25% dibagi rata ke semua agent COPIC
                $count = count($agents);
                if ($count < 1) {
                    continue;
                }
                $perRate = $rate / $count;

                foreach ($agents as $agId) {
                    $pendapatan = (int) round($basisPendapatan * $perRate);
                    if ($pendapatan <= 0) {
                        continue;
                    }

                    $row = TransactionCommission::updateOrCreate(
                        [
                            'id_transaction' => $idTransaksi,
                            'role'           => $kode,
                            'id_agent'       => $agId,
                        ],
                        [
                            'pendapatan'     => $pendapatan,
                        ]
                    );

                    $keptIds[] = $row->id;
                }
            } else {
                // Kode lain: masing2 agent (biasanya cuma 1) dapat full rate
                foreach ($agents as $agId) {
                    $pendapatan = (int) round($basisPendapatan * $rate);
                    if ($pendapatan <= 0) {
                        continue;
                    }

                    $row = TransactionCommission::updateOrCreate(
                        [
                            'id_transaction' => $idTransaksi,
                            'role'           => $kode,
                            'id_agent'       => $agId,
                        ],
                        [
                            'pendapatan'     => $pendapatan,
                        ]
                    );

                    $keptIds[] = $row->id;
                }
            }
        }

        // Hapus row yang tidak lagi relevan (misal COPIC dulu 3 agent, sekarang tinggal 2)
        if (!empty($keptIds)) {
            TransactionCommission::where('id_transaction', $idTransaksi)
                ->whereNotIn('id', $keptIds)
                ->delete();
        } else {
            // Safety: kalau aneh hasilnya kosong → buang semua
            TransactionCommission::where('id_transaction', $idTransaksi)->delete();
        }
    }

    // --- 6. Update status property & listing similar menjadi 'Terjual' ---
    //    - current listing
    //    - semua listing lain yg similar (sertifikat + luas + kota)
    $similarListingIds = $rows
        ->pluck('id_listing')
        ->filter()
        ->unique()
        ->values()
        ->all(); // contoh: [300, 400, 58183]

    if (!empty($similarListingIds)) {
        Property::whereIn('id_listing', $similarListingIds)
            ->update([
                'status'          => 'Terjual',
                'tanggal_diupdate'=> now(),
            ]);
    }

    return back()->with('success', 'Status transaksi berhasil disimpan.');
}





/**
 * Generate ID transaksi dengan prefix TR + angka berurutan.
 * Contoh: TR000001, TR000002, dst.
 */

}
