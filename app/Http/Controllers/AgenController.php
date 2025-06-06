<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Agen;
use App\Models\HargaAgen;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;


class AgenController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Agen',
            'list' => ['Home', 'Agen']
        ];

        $title = 'agen';
        $agen = Agen::all();
        $activeMenu = 'agen';
        $daftarKota = Agen::select('kota')->distinct()->pluck('kota');


        return view('agen.index', ['title' => $title, 'daftarKota' => $daftarKota, 'breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'agen' => $agen]);
    }

    public function list(Request $request)
    {
        $agen = Agen::select('agen_id', 'nama', 'email', 'no_telf', 'alamat', 'kecamatan', 'kota', 'provinsi')
            ->with('transaksi');

        if ($request->kota && $request->kota != '') {
            $agen->where('kota', $request->kota);
        }

        return DataTables::of($agen)
            ->addIndexColumn()
            ->addColumn('aksi', function ($agen) { // menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/edit") . '\')" class="btn btn-info"><i class="fas fa-edit"></i> Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url("agen/$agen->agen_id/delete").'\')" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah HTML
            ->make(true);
    }

    public function create()
    {
        return view('agen.add');
    }

    public function store(Request $request){
        //cek apsakah request berupa ajax
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'email' => 'required|unique:m_agen,email',
                'nama' => 'required|string|max:200',
                'alamat' => 'required|string',
                'kecamatan' => 'required|string',
                'kota' => 'required|string',
                'provinsi' => 'required|string',
                'no_telf' => 'required|string|max:15',
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                return response()->json([
                'status' => false, // response status, false: error/gagal, true: berhasil
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(), // pesan error validasi
                ]);
            }

            DB::beginTransaction();
            try {
                // Simpan data agen
                $agen = Agen::create($request->all());

                // Ambil semua data barang
                $barangs = Barang::all();

                // Simpan harga agen default untuk setiap barang
                foreach ($barangs as $barang) {
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
                    'message' => 'Data agen dan harga agen berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Data agen tidak berhasil disimpan',
                    // 'error' => $e->getMessage(), // Bisa dihapus jika tak ingin tampilkan error ke client
                ]);
            }
        }
        return redirect('/');
    }

    // public function show($id)
    // {
    //     $agen = Agen::findOrFail($id);
    //     return response()->json($agen);
    // }

    public function show($id)
    {
        // Ambil data agen dengan relasi hargaAgen dan transaksi + detail transaksi + barang
        $agen = Agen::with([
            // 'hargaAgen' => function ($q) {
            //     $q->whereNotNull('id'); // pastikan hanya data valid
            // },
            'hargaAgen.barang',
            'transaksi.detailTransaksi.barang'
        ])->findOrFail($id);

         if (!$agen) {
            return view('agen.show', ['agen' => null]);
        }

        return view('agen.detail', [
            'agen' => $agen,
            'harga_produk' => $agen->hargaAgen,
            'transaksi' => $agen->transaksi,
        ]);
    }

    public function update_harga(Request $request, $id)
    {
        $request->validate([
            'harga' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0',
            'diskon_persen' => 'required|numeric|min:0',
        ]);

        $hargaAgen = HargaAgen::findOrFail($id);
        $hargaAgen->harga = $request->harga;
        $hargaAgen->diskon = $request->diskon;
        $hargaAgen->diskon_persen = $request->diskon_persen;

        $hargaAgen->save();

        return back()->with('success', 'Harga dan diskon berhasil diperbarui.');
    }


    public function edit($id)
    {
        $agen = Agen::findOrFail($id);
        return view('agen.edit', compact('agen'));
    }

    public function update(Request $request, $id) {
         $agen = Agen::find($id);
        if (!$agen) {
            return response()->json([
                'status' => false,
                'message' => 'Data agen tidak ditemukan.'
            ], 404);
        }
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'email' => 'required',
                'nama' => 'required|string|max:200',
                'alamat' => 'required|string',
                'kecamatan' => 'required|string',
                'kota' => 'required|string',
                'provinsi' => 'required|string',
                'no_telf' => 'required|string|max:15',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = Agen::find($id);

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

    public function destroy($id)
    {
        $agen = Agen::findOrFail($id);
        $agen->delete();

        return response()->json(['message' => 'Agen berhasil dihapus.']);
    }

}
