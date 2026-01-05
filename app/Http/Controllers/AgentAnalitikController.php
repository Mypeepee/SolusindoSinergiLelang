<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentAnalitikController extends Controller
{
    public function widgetsKpis(Request $request, string $id_agent)
    {
        return $this->kpis($request, $id_agent);
    }

    private function parseRange(Request $request): array
    {
        // Prioritas: year -> preset -> custom
        $year   = (int)($request->query('year') ?: now()->year);
        $preset = $request->query('preset', 'year'); // year | last12 | custom

        if ($preset === 'custom') {
            $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfDay() : Carbon::create($year,1,1)->startOfDay();
            $end   = $request->query('end')   ? Carbon::parse($request->query('end'))->endOfDay()     : Carbon::create($year,12,31)->endOfDay();
        } elseif ($preset === 'last12') {
            $end   = now()->endOfDay();
            $start = now()->subMonths(11)->startOfMonth()->startOfDay();
        } else { // 'year'
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();
        }

        return [$start, $end, $year, $preset];
    }

    private function baseTrxQuery(string $idAgent, Request $request)
    {
        [$start, $end] = $this->parseRange($request);

        $q = DB::table('transaction')
            ->where('id_agent', $idAgent)
            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);

        // optional filters
        if ($request->filled('status')) {
            $statuses = (array)$request->query('status');
            $q->whereIn('status_transaksi', $statuses);
        }

        if ($request->filled('skema')) {
            $skemas = (array)$request->query('skema');
            $q->whereIn('skema_komisi', $skemas);
        }

        if ($request->filled('min_deal')) {
            $q->where('harga_deal', '>=', (int)$request->query('min_deal'));
        }

        return $q;
    }

    public function show(Request $request, string $id_agent)
    {
        $agent = DB::table('agent')->where('id_agent', $id_agent)->first();

        $minYear = (int)(DB::table('transaction')->selectRaw("MIN(date_part('year', tanggal_transaksi)) as y")->value('y') ?? now()->year);
        $maxYear = (int)(DB::table('transaction')->selectRaw("MAX(date_part('year', tanggal_transaksi)) as y")->value('y') ?? now()->year);
        $availableYears = range($maxYear, $minYear);

        [$start, $end, $year, $preset] = $this->parseRange($request);

        $uplineId = null;
        try { $uplineId = $agent->upline_id ?? $agent->upline ?? null; } catch (\Throwable $e) {}
        $upline = $uplineId ? DB::table('agent')->where('id_agent', $uplineId)->first() : null;

        // ✅ KPI ringkas (agar HERO langsung tampil tanpa AJAX)
        $trxQ = $this->baseTrxQuery($id_agent, $request);

        $trxSummary = (clone $trxQ)->selectRaw("
            COUNT(*) as trx_count,
            COALESCE(SUM(harga_deal),0) as omzet,
            COALESCE(SUM(basis_pendapatan),0) as pendapatan_kotor,
            COALESCE(AVG(kenaikan_dari_limit),0) as avg_kenaikan
        ")->first();

        $incomeAgent = DB::table('transaction_commissions as tc')
            ->join('transaction as t', 't.id_transaction', '=', 'tc.id_transaction')
            ->where('tc.id_agent', $id_agent)
            ->whereBetween('t.tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('status'), fn($q)=>$q->whereIn('t.status_transaksi', (array)$request->query('status')))
            ->when($request->filled('skema'), fn($q)=>$q->whereIn('t.skema_komisi', (array)$request->query('skema')))
            ->when($request->filled('min_deal'), fn($q)=>$q->where('t.harga_deal','>=',(int)$request->query('min_deal')))
            ->selectRaw("COALESCE(SUM(tc.pendapatan),0) as income")
            ->value('income');

        $trxCount = (int)($trxSummary->trx_count ?? 0);

        return view('detailagent.detailagent', compact(
            'agent','upline','availableYears','year','preset','start','end',
            'trxSummary','incomeAgent','trxCount'
        ));
    }

    public function kpis(Request $request, string $id_agent)
    {
        [$start, $end, $year, $preset] = $this->parseRange($request);

        $trxQ = $this->baseTrxQuery($id_agent, $request);

        $trxSummary = (clone $trxQ)->selectRaw("
            COUNT(*) as trx_count,
            COALESCE(SUM(harga_deal),0) as omzet,
            COALESCE(SUM(basis_pendapatan),0) as pendapatan_kotor,
            COALESCE(AVG(kenaikan_dari_limit),0) as avg_kenaikan,
            COALESCE(SUM(cobroke_fee),0) as total_cobroke,
            COALESCE(SUM(royalty_fee),0) as total_royalty
        ")->first();

        // Income agent dari transaction_commissions (agent ini bisa jadi THC / UP / PIC / dll)
        $incomeAgent = DB::table('transaction_commissions as tc')
            ->join('transaction as t', 't.id_transaction', '=', 'tc.id_transaction')
            ->where('tc.id_agent', $id_agent)
            ->whereBetween('t.tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('status'), fn($q)=>$q->whereIn('t.status_transaksi', (array)$request->query('status')))
            ->when($request->filled('skema'), fn($q)=>$q->whereIn('t.skema_komisi', (array)$request->query('skema')))
            ->when($request->filled('min_deal'), fn($q)=>$q->where('t.harga_deal','>=',(int)$request->query('min_deal')))
            ->selectRaw("COALESCE(SUM(tc.pendapatan),0) as income")
            ->value('income');

        // Rekrut langsung (jika ada upline_id)
        $directRecruit = 0;
        $activeDownline = 0;
        try {
            $directRecruit = (int)DB::table('agent')->where('upline_id', $id_agent)->count();

            // downline aktif = downline yang punya transaksi di periode
            $downlineIds = DB::table('agent')->where('upline_id', $id_agent)->pluck('id_agent')->toArray();
            if (!empty($downlineIds)) {
                $activeDownline = (int)DB::table('transaction')
                    ->whereIn('id_agent', $downlineIds)
                    ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
                    ->distinct('id_agent')->count('id_agent');
            }
        } catch (\Throwable $e) {}

        return view('detailagent._kpis', [
            'trxSummary'     => $trxSummary,
            'incomeAgent'    => $incomeAgent,
            'directRecruit'  => $directRecruit,
            'activeDownline' => $activeDownline,
            'year'           => $year,
            'preset'         => $preset,
            'start'          => $start,
            'end'            => $end,
        ]);
    }

    public function trend(Request $request, string $id_agent)
    {
        [$start, $end] = $this->parseRange($request);

        $trxQ = $this->baseTrxQuery($id_agent, $request);

        // group by month
        $rows = (clone $trxQ)
            ->selectRaw("
                to_char(tanggal_transaksi, 'YYYY-MM') as ym,
                COUNT(*) as trx_count,
                COALESCE(SUM(harga_deal),0) as omzet,
                COALESCE(SUM(basis_pendapatan),0) as gross
            ")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = [];
        $omzet  = [];
        $gross  = [];
        $trx    = [];

        foreach ($rows as $r) {
            $labels[] = $r->ym;
            $omzet[]  = (int)$r->omzet;
            $gross[]  = (int)$r->gross;
            $trx[]    = (int)$r->trx_count;
        }

        return response()->json([
            'labels' => $labels,
            'omzet'  => $omzet,
            'gross'  => $gross,
            'trx'    => $trx,
        ]);
    }

    public function roles(Request $request, string $id_agent)
    {
        [$start, $end] = $this->parseRange($request);

        $q = DB::table('transaction_commissions as tc')
            ->join('transaction as t', 't.id_transaction', '=', 'tc.id_transaction')
            ->where('tc.id_agent', $id_agent)
            ->whereBetween('t.tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
            ->when($request->filled('status'), fn($qq)=>$qq->whereIn('t.status_transaksi', (array)$request->query('status')))
            ->when($request->filled('skema'), fn($qq)=>$qq->whereIn('t.skema_komisi', (array)$request->query('skema')))
            ->when($request->filled('min_deal'), fn($qq)=>$qq->where('t.harga_deal','>=',(int)$request->query('min_deal')))
            ->selectRaw("tc.role as role, COALESCE(SUM(tc.pendapatan),0) as total, COUNT(*) as cnt")
            ->groupBy('tc.role')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $q->pluck('role'),
            'values' => $q->pluck('total')->map(fn($v)=>(int)$v),
            'counts' => $q->pluck('cnt')->map(fn($v)=>(int)$v),
        ]);
    }

    public function transactions(Request $request, string $id_agent)
    {
        [$start, $end] = $this->parseRange($request);

        $trxQ = $this->baseTrxQuery($id_agent, $request);

        $rows = (clone $trxQ)
            ->select([
                'id_transaction','id_listing','tanggal_transaksi','status_transaksi','skema_komisi',
                'harga_deal','harga_bidding','basis_pendapatan','selisih','kenaikan_dari_limit','cobroke_fee','royalty_fee'
            ])
            ->orderByDesc('tanggal_transaksi')
            ->limit(50)
            ->get();

        return view('detailagent._transactions', [
            'rows'  => $rows,
            'start' => $start,
            'end'   => $end,
        ]);
    }
}
