<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Agen;
use App\Models\HargaAgen;

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
                    '/show') . '\')" class="btn btn-outline-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/edit') . '\')" class="btn btn-outline-info"><i class="fas fa-edit"></i> Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id .
                    '/delete') . '\')" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button> ';
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
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kode_barang' => 'required|unique:m_barang,kode_barang',
                'nama_barang' => 'required',
                'kalori' => 'required',
                'komposisi' => 'required',
                'kandungan' => 'required',
                'ukuran' => 'required',
                'pic' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'stok' => 'nullable|integer',
                'hpp' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $data = $request->except('pic');

            if ($request->hasFile('pic')) {
                $file = $request->file('pic');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/barang'), $filename);
                $data['pic'] = $filename;
            }
            
            DB::beginTransaction();

            try {
                $barang = Barang::create($data);
                $agens = Agen::all();

                foreach ($agens as $agen) {
                    HargaAgen::create([
                        'agen_id' => $agen->agen_id,
                        'barang_id' => $barang->barang_id,
                        'harga' => 0,
                        'diskon' => 0,
                        'diskon_persen' => 0,
                    ]);
                }
                DB::commit();

                 return response()->json([
                    'status' => true,
                    'message' => 'Data barang berhasil disimpan'
                ]);

            }  catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Data barang tidak berhasil disimpan',
                    'error' => $e->getMessage(), // Bisa dihapus jika tak ingin tampilkan error ke client
                ]);
            }
        }
        return redirect('/');
    }

    public function show($id)
    {
        $barang = Barang::with([
            'detailTransaksi.transaksi',
            'detailTransaksiMasuk.purchase'
        ])->findOrFail($id);

        // Urutkan histori keluar berdasarkan tgl_transaksi (dari relasi transaksi)
        $histori_keluar = $barang->detailTransaksi->sortByDesc(function ($item) {
            return $item->transaksi->tgl_transaksi ?? $item->created_at;
        });

        // Urutkan histori masuk berdasarkan tgl_transaksi (dari relasi purchase)
        $histori_masuk = $barang->detailTransaksiMasuk->sortByDesc(function ($item) {
            return $item->purchase->tgl_transaksi ?? $item->created_at;
        });

        return view('stok_barang.show', [
            'barang' => $barang,
            'histori_keluar' => $histori_keluar,
            'histori_masuk' => $histori_masuk
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

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_barang' => 'required',
                'kalori' => 'required',
                'komposisi' => 'required',
                'kandungan' => 'required',
                'ukuran' => 'required',
                'pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'stok' => 'required|integer',
                'hpp' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $data = $request->except('pic');

            if ($request->hasFile('pic')) {
                // Hapus gambar lama jika ada dan file-nya ada di disk
                $gambarLama = public_path('uploads/barang/' . $barang->pic);
                if ($barang->pic && file_exists($gambarLama)) {
                    unlink($gambarLama);
                }

                // Simpan gambar baru
                $file = $request->file('pic');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/barang'), $filename);
                $data['pic'] = $filename;
            }

            $barang->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ]);
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
                    // Hapus file gambar jika ada dan file-nya masih tersedia
                    $gambarPath = public_path('uploads/barang/' . $barang->pic);
                    if ($barang->pic && file_exists($gambarPath)) {
                        unlink($gambarPath);
                    }

                    // Hapus data dari database
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
