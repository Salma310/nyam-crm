<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        return view('stok_barang.index');
    }

    public function list(Request $request)
    {
        $data = Barang::select('barang_id', 'kode_barang', 'nama_barang', 'stok', 'harga_jual', 'harga_beli');

        return DataTables::of($data)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) { 
                $btn  = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        return view('stok_barang.form', ['barang' => new Barang()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|unique:barang,kode_produk',
            'nama_produk' => 'required',
            'stok' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ]);

        Barang::create($request->all());

        return response()->json(['message' => 'Produk berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json($barang);
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('stok_barang.form', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'kode_produk' => 'required|unique:barang,kode_produk,' . $barang->id,
            'nama_produk' => 'required',
            'stok' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ]);

        $barang->update($request->all());

        return response()->json(['message' => 'Produk berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
