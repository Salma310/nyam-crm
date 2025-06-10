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
    public function index()
    {
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
            'labels' => $bulanLabels,
            'data' => $dataChart,
            'topBarang' => $topBarang,
            'topAgen' => $topAgen,
            'inactiveAgents' => $inactiveAgents
        ]);
    }
}
