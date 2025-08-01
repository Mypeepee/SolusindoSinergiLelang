<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\InformasiKlien;
use App\Models\PropertyInterest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Property;
use Illuminate\Support\Facades\Artisan;

class AgentAdminController extends Controller
{
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

        $totalKomisi = DB::table('transaction')
            ->where('id_agent', $idAgent ?? $idAccount)
            ->sum('komisi_agent');

        $totalSelisih = DB::table('transaction')
            ->where('id_agent', $idAgent ?? $idAccount)
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
        $salesData = $salesData ?? [];
        $transactions = $transactions ?? [];
        // Siapkan label bulan
$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Ambil pendapatan & jumlah transaksi
$earnings = DB::table('transaction')
    ->select(
        DB::raw('EXTRACT(YEAR FROM tanggal_transaksi) AS year'),
        DB::raw('EXTRACT(MONTH FROM tanggal_transaksi) AS month'),
        DB::raw('SUM(selisih) AS total'),
        DB::raw('COUNT(*) as total_transaksi')
    )
    ->where('id_agent', $idAgent ?? $idAccount)
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

// Kirim ke view
return view('Agent.dashboard-agent', [
    'totalKomisi' => $totalKomisi,
    'totalSelisih' => $totalSelisih,
    'jumlahListing' => $jumlahListing,
    'jumlahClients' => $jumlahClients,
    'clients' => $clients,
    'clientsClosing' => $clientsClosing,
    'clientsPengosongan' => $clientsPengosongan,
    'salesData' => json_encode($salesData),         // <--- dijamin ada
    'transactions' => json_encode($transactions),
    'labels' => $labels,
    'revenue' => $revenue,
    'transactions' => $transactions,
    'statusCounts' => (array) $statusCounts,
    'pendingAgents' => $pendingAgents,
]);
    }


    public function owner()
    {
        //property types
        // Semua tipe properti yang mau ditampilkan
        $propertyTypes = ['Rumah', 'Gudang', 'Apartemen', 'Tanah', 'Pabrik', 'Hotel dan Villa', 'Ruko', 'Sewa'];

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

        // performance
        $performanceAgents = DB::table('agent')
            ->leftJoin('property', 'agent.id_agent', '=', 'property.id_agent')
            ->leftJoin('transaction', 'agent.id_agent', '=', 'transaction.id_agent')
            ->select(
                'agent.id_agent',
                'agent.nama',
                'agent.jumlah_penjualan',
                'agent.status',
                DB::raw('COUNT(DISTINCT property.id_listing) as jumlah_listing'),
                DB::raw('SUM(transaction.komisi_agent) as total_komisi')
            )
            ->groupBy('agent.id_agent', 'agent.nama', 'agent.jumlah_penjualan')
            ->get();

        //grafik
        $monthlyData = DB::table('transaction')
            ->selectRaw("DATE_TRUNC('month', tanggal_dibuat) as bulan, SUM(harga_deal) as total_pendapatan, COUNT(*) as total_transaksi")
            ->groupByRaw("DATE_TRUNC('month', tanggal_dibuat)")
            ->orderByRaw("DATE_TRUNC('month', tanggal_dibuat)")
            ->get();

        $labels = $monthlyData->map(fn($d) => \Carbon\Carbon::parse($d->bulan)->isoFormat('MMM YYYY'));
        $revenue = $monthlyData->pluck('total_pendapatan');
        $transactions = $monthlyData->pluck('total_transaksi');

        //piechart
        $statusCounts = PropertyInterest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('Agent.dashboardowner', ['pendingAgents' => $pendingAgents,
                                            'pendingClients' => $pendingClients,
                                            'clients' => $clients,
                                            'clientsClosing' => $clientsClosing,
                                            'clientsPengosongan' => $clientsPengosongan,
                                            'performanceAgents' => $performanceAgents,
                                            'properties' => $properties,
                                            'labels' => $labels,
                                            'revenue' => $revenue,
                                            'transactions' => $transactions,
                                            'statusCounts' => $statusCounts ]);
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

                return redirect()->back()->with('success', '✅ Transaksi Closing berhasil disimpan!');
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

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
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


}
