<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        return view('stok_barang.index');
    }

    public function list(Request $request)
    {
        $data = Barang::select('barang_id', 'kode_barang', 'nama_barang', 'stok', 'hpp');

        return DataTables::of($data)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn  = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/show') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/edit') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/delete') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        return view('stok_barang.add');
    }

    public function store(Request $request)
    {
        //cek apsakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kode_barang' => 'required|unique:m_barang,kode_barang',
                'nama_barang' => 'required',
                'kalori' => 'required',
                'komposisi' => 'required',
                'kandungan' => 'required',
                'ukuran' => 'required',
                'pic' => 'required',
                'stok' => 'required|integer',
                'hpp' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // response status, false: error/gagal, true: berhasil
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(), // pesan error validasi
                ]);
            }

            Barang::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function show($id)
    {
        $barang = Barang::with(['detailTransaksi.transaksi', 'detailTransaksiMasuk.purchase'])->findOrFail($id);

        return view('stok_barang.show', [
            'barang' => $barang,
            'histori_keluar' => $barang->detailTransaksi,
            'histori_masuk' => $barang->detailTransaksiMasuk
        ]);
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('stok_barang.edit', ['barang' => $barang]);
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Data barang tidak ditemukan.'
            ], 404);
        }
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_barang' => 'required',
                'kalori' => 'required',
                'komposisi' => 'required',
                'kandungan' => 'required',
                'ukuran' => 'required',
                'pic' => 'required',
                'stok' => 'required|integer',
                'hpp' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = Barang::find($id);

            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function confirm(string $barang_id)
    {
        $barang = Barang::find($barang_id);
        return view('stok_barang.confirm', ['barang' => $barang],);
    }

    public function delete(Request $request, $barang_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = Barang::find($barang_id);

            if ($barang) {
                try {
                    $barang->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }
}
