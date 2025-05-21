<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Agen;

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

            Agen::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data agen berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function show($id)
    {
        $agen = Agen::findOrFail($id);
        return response()->json($agen);
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
