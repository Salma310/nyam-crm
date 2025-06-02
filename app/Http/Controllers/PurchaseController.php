<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Purchase;
use App\Models\Barang;
use App\Models\DetailPurchase;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PurchaseController extends Controller
{
    //
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Agen',
            'list' => ['Home', 'Agen']
        ];

        $title = 'purchase';
        $purchase = Purchase::all();
        $barang = Barang::all();
        $detailPurchase = DetailPurchase::all();

        $activeMenu = 'purchase';

        return view('purchase.index', [
            'title' => $title,
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'purchase' => $purchase,
            'barang' => $barang,
            'detailPurchase' => $detailPurchase
        ]);
    }

    public function list(Request $request)
    {
        $purchase = Purchase::with(['detailPurchase', 'detailPurchase.barang']);

        return DataTables::of($purchase)
            ->addIndexColumn()
            // ->addColumn('nama_agen', function ($t) {
            //     return $t->agen->nama ?? '-'; // atau ->nama, sesuai nama kolom kamu
            // })
            ->addColumn('nama_barang', function ($t) {
                return $t->detailPurchase->map(function ($detail) {
                    return $detail->barang->nama_barang ?? '-';
                })->implode(', ');
            })
            ->addColumn('total_qty', function ($t) {
                return $t->detailPurchase->sum('qty');
            })
            ->addColumn('aksi', function ($t) {
                $btn = '<button onclick="modalAction(\'' . url("purchase/$t->transaksi_masuk_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show($id)
    {
        // Ambil transaksi lengkap dengan relasi agen, detail, dan barang
        $purchase = Purchase::with(['detailPurchase', 'detailPurchase.barang'])->findOrFail($id);
        // $agenId = $transaksi->agen_id;

        $totalHarga = 0;

        $detailHarga = [];

        foreach ($purchase->detailPurchase as $detail) {
            $barangId = $detail->barang_id;

            $hargaBarang = Barang::where('barang_id', $barangId)->first();
            $hargaSatuan = $hargaBarang->hpp;

            // $hargaAgen = HargaAgen::where('agen_id', $agenId)
            //     ->where('barang_id', $barangId)
            //     ->first();

            // if ($hargaBarang) {
            //     $hargaSatuan = $hargaBarang->hpp;
            //     $diskon = $hargaAgen->diskon + (($hargaSatuan * $hargaAgen->diskon_persen) / 100);
            //     $pajak = $hargaAgen->pajak;

            //     $hargaSetelahDiskon = $hargaSatuan - $diskon;
            //     $hargaFinal = ($hargaSetelahDiskon + $pajak) * $detail->qty;

            //     $totalHarga += $hargaFinal;

            //     $detailHarga[] = [
            //         'detail' => $detail,
            //         'harga_satuan' => $hargaSatuan,
            //         'diskon' => $diskon,
            //         'pajak' => $pajak,
            //         'harga_final' => $hargaFinal,
            //         'hpp' => $detail->barang->hpp ?? 0,
            //     ];
            // }
        }

        return view('purchase.detail', compact('purchase', 'hargaSatuan'));
    }
}
