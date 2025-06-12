<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterChart = $request->get('filter_chart');

        $data = [];


        if (!$filterChart || $filterChart === 'transaksi') {
            $startMonth = $request->get('start_month', now()->subMonths(11)->format('Y-m'));
            $endMonth = $request->get('end_month', now()->format('Y-m'));

            [$labels, $dataPoints] = $this->getMonthlyTransactionData($startMonth, $endMonth);
            $data['labels'] = $labels;
            $data['data'] = $dataPoints;
        }

        if (!$filterChart || $filterChart === 'barang') {
            $tanggalBarang = explode(' - ', $request->get('tanggal_barang', now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->endOfMonth()->format('Y-m-d')));
            $data['topBarang'] = $this->getTopBarang($tanggalBarang);
        }

        if (!$filterChart || $filterChart === 'agen') {
            $tanggalAgen = explode(' - ', $request->get('tanggal_agen', now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->endOfMonth()->format('Y-m-d')));
            $data['topAgen'] = $this->getTopAgen($tanggalAgen);
        }

        // Total revenue (total harga dari semua transaksi)
        $totalRevenue = Transaksi::sum('harga_total');

        // Total produk terjual (dari tabel detail transaksi)
        $totalProductsSold = DetailTransaksi::sum('qty');

        // Total produk
        $totalProducts = Barang::count();

        // Total agen
        $totalAgents = Agen::count();

        // Transaksi per bulan (12 bulan terakhir)
        $transaksiPerBulan = Transaksi::select(
            DB::raw("DATE_FORMAT(tgl_transaksi, '%Y-%m') as bulan"),
            DB::raw("COUNT(*) as total")
        )
            ->where('tgl_transaksi', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Format label dan data untuk chart
        $bulanLabels = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths(11 - $i)->format('M Y');
        });

        $dataChart = $bulanLabels->map(function ($label) use ($transaksiPerBulan) {
            $match = $transaksiPerBulan->firstWhere('bulan', Carbon::createFromFormat('M Y', $label)->format('Y-m'));
            return $match ? $match->total : 0;
        });

        // Barang terlaris (top 5)
        $topBarang = DetailTransaksi::select('barang_id', DB::raw('SUM(qty) as total_terjual'))
            ->groupBy('barang_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->with('barang:barang_id,nama_barang')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'total_terjual' => $item->total_terjual
                ];
            });

        // Agen teraktif (top 5)
        $topAgen = Transaksi::select('agen_id', DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('agen_id')
            ->orderByDesc('total_transaksi')
            ->take(5)
            ->with('agen:agen_id,nama')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->agen->agen_id ?? null,
                    'nama' => $item->agen->nama ?? '-',
                    'total_transaksi' => $item->total_transaksi
                ];
            });

        // Agen tidak aktif 30 hari terakhir
        $inactiveAgents = Agen::whereDoesntHave('transaksi', function ($query) {
            $query->where('tgl_transaksi', '>=', now()->subDays(30));
        })
            ->with(['transaksi' => function ($q) {
                $q->latest('tgl_transaksi')->limit(1);
            }])
            ->get()
            ->map(function ($agen) {
                return (object) [
                    'id' => $agen->agen_id,
                    'nama' => $agen->nama,
                    'terakhir_transaksi' => optional($agen->transaksi->first())->tgl_transaksi
                ];
            });

        return view('dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalProductsSold' => $totalProductsSold,
            'totalProducts' => $totalProducts,
            'totalAgents' => $totalAgents,
            'labels' => $data['labels'] ?? [],
            'data' => $data['data'] ?? [],
            'topBarang' => $data['topBarang'] ?? [],
            'topAgen' => $data['topAgen'] ?? [],
            'inactiveAgents' => $inactiveAgents
        ]);
    }

    public function filter(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        if (!$startMonth || !$endMonth) {
            return response()->json([
                'message' => 'Periode tidak valid.'
            ], 422);
        }

        [$labels, $dataPoints] = $this->getMonthlyTransactionData($startMonth, $endMonth);

        return response()->json([
            'labels' => $labels,
            'data' => $dataPoints,
        ]);
    }

    public function filterTopBarang(Request $request)
    {
        $tanggalBarang = explode(' - ', $request->input('tanggal_barang'));
        if (count($tanggalBarang) !== 2) {
            return response()->json(['message' => 'Format tanggal tidak valid.'], 422);
        }

        $topBarang = $this->getTopBarang($tanggalBarang);

        return response()->json([
            'labels' => $topBarang->pluck('nama_barang'),
            'data' => $topBarang->pluck('total_terjual'),
        ]);
    }

    public function filterTopAgen(Request $request)
    {
        $tanggalAgen = explode(' - ', $request->input('tanggal_agen'));
        if (count($tanggalAgen) !== 2) {
            return response()->json(['message' => 'Format tanggal tidak valid.'], 422);
        }

        $topAgen = $this->getTopAgen($tanggalAgen);

        return response()->json([
            'labels' => $topAgen->pluck('nama'),
            'data' => $topAgen->pluck('total_transaksi'),
            'jumlah_transaksi' => $topAgen->pluck('jumlah_transaksi'),
        ]);
    }

    private function getMonthlyTransactionData($startMonth, $endMonth)
    {
        $start = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();

        $allMonths = [];
        $current = $start->copy();
        while ($current <= $end) {
            $allMonths[] = $current->format('Y-m');
            $current->addMonth();
        }

        $transactions = Transaksi::select(
            DB::raw("DATE_FORMAT(tgl_transaksi, '%Y-%m') as bulan"),
            DB::raw("COUNT(*) as total")
        )
            ->whereBetween('tgl_transaksi', [$start, $end])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels = [];
        $dataPoints = [];

        foreach ($allMonths as $month) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $dataPoints[] = $transactions[$month] ?? 0;
        }

        return [$labels, $dataPoints];
    }

    private function getTopBarang(array $tanggalBarang)
    {
        $start = Carbon::parse($tanggalBarang[0])->startOfDay();
        $end = Carbon::parse($tanggalBarang[1])->endOfDay();

        return DetailTransaksi::whereHas('transaksi', function ($query) use ($start, $end) {
            $query->whereBetween('tgl_transaksi', [$start, $end]);
        })
            ->select('barang_id', DB::raw('SUM(qty) as total_terjual'))
            ->groupBy('barang_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->with('barang:barang_id,nama_barang')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'total_terjual' => $item->total_terjual
                ];
            });
    }

    private function getTopAgen(array $tanggalAgen)
    {
        $start = Carbon::parse($tanggalAgen[0])->startOfDay();
        $end = Carbon::parse($tanggalAgen[1])->endOfDay();

        return Transaksi::whereBetween('tgl_transaksi', [$start, $end])
            ->select('agen_id', DB::raw('SUM(harga_total) as total_transaksi'), DB::raw('COUNT(*) as jumlah_transaksi'))
            ->groupBy('agen_id')
            ->orderByDesc('total_transaksi')
            ->take(5)
            ->with('agen:agen_id,nama')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->agen->agen_id ?? null,
                    'nama' => $item->agen->nama ?? '-',
                    'total_transaksi' => $item->total_transaksi,
                    'jumlah_transaksi' => $item->jumlah_transaksi
                ];
            });
    }
}
