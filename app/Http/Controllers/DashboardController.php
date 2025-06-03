<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Transaksi per bulan (langkah 1 tetap)
        $transaksiPerBulan = DB::table('t_transaksi')
            ->selectRaw('MONTH(tgl_transaksi) as bulan, COUNT(*) as total')
            ->groupByRaw('MONTH(tgl_transaksi)')
            ->orderByRaw('bulan')
            ->get();

        $labels = [];
        $data = [];
        foreach ($transaksiPerBulan as $row) {
            $labels[] = Carbon::create()->month($row->bulan)->locale('id')->isoFormat('MMMM');
            $data[] = $row->total;
        }

        // Agen tidak aktif > 30 hari
        $inactiveAgents = DB::table('m_agen')
            ->leftJoin('t_transaksi', 'm_agen.agen_id', '=', 't_transaksi.agen_id')
            ->select('m_agen.nama', 'm_agen.email', DB::raw('MAX(t_transaksi.tgl_transaksi) as terakhir_transaksi'))
            ->groupBy('m_agen.agen_id', 'm_agen.nama', 'm_agen.email')
            ->havingRaw('terakhir_transaksi IS NULL OR DATEDIFF(CURDATE(), terakhir_transaksi) > 30')
            ->get();

        // Top barang paling banyak terjual
        $topBarang = DB::table('t_detail_transaksi')
            ->join('m_barang', 't_detail_transaksi.barang_id', '=', 'm_barang.barang_id')
            ->select('m_barang.nama_barang', DB::raw('SUM(t_detail_transaksi.qty) as total_terjual'))
            ->groupBy('m_barang.barang_id', 'm_barang.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Top agen
        $topAgen = DB::table('t_transaksi')
            ->join('m_agen', 't_transaksi.agen_id', '=', 'm_agen.agen_id')
            ->select('m_agen.nama', DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('m_agen.agen_id', 'm_agen.nama')
            ->orderByDesc('total_transaksi')
            ->limit(5)
            ->get();

        return view('dashboard', compact('labels', 'data', 'inactiveAgents', 'topBarang', 'topAgen'));
    }
}
