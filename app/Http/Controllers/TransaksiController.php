<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Agen;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class TransaksiController extends Controller
{
    //
     public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Agen',
            'list' => ['Home', 'Agen']
        ];

        $title = 'agen';
        $transaksi = Transaksi::all();
        $barang = Barang::all();
        $detailTransaksi = DetailTransaksi::all();
        $agen = Agen::all();

        $activeMenu = 'transaksi';

        return view('transaksi.index', ['title' => $title, 'breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 
            'agen' => $agen, 'transaksi' => $transaksi, 'barang' => $barang, 'detailTransaksi' => $detailTransaksi]);
    }

    public function list(Request $request)
    {
        $transaksi = Transaksi::with(['agen', 'detailTransaksi']);

        return DataTables::of($transaksi)
            ->addIndexColumn()
            ->addColumn('nama_agen', function ($t) {
                return $t->agen->nama ?? '-'; // atau ->nama, sesuai nama kolom kamu
            })
            ->addColumn('total_qty', function ($t) {
                return $t->detailTransaksi->sum('qty');
            })
            ->addColumn('aksi', function ($t) {
                $btn = '<button onclick="modalAction(\'' . url("transaksi/$t->transaksi_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                // $btn .= '<button onclick="modalAction(\'' . url("transaksi/$t->transaksi_id/edit") . '\')" class="btn btn-info"><i class="fas fa-edit"></i> Edit</button> ';
                // $btn .= '<button onclick="modalAction(\'' . url("transaksi/$t->transaksi_id/delete") . '\')" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show($id)
    {
        // Ambil transaksi sekaligus relasi agen dan detail transaksi + barang
        $transaksi = Transaksi::with(['agen', 'detailTransaksi.barang'])->findOrFail($id);

        // Hitung total harga (jumlahkan harga_jual * qty di detail transaksi)
        $totalHarga = 0;
        foreach ($transaksi->detailTransaksi as $detail) {
            $totalHarga += $detail->barang->harga_jual * $detail->qty;
        }

        return view('transaksi.detail', compact('transaksi', 'totalHarga'));
    }


    public function printInvoice($id)
    {
        $transaksi = Transaksi::with(['agen', 'detailTransaksi.barang'])->findOrFail($id);

        $pdf = PDF::loadView('transaksi.invoice', compact('transaksi'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('invoice-' . $transaksi->kode_transaksi . '.pdf');
    }

}
