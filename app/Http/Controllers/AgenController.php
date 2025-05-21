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


        return DataTables::of($agen)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            // ->addColumn('role_nama', function ($user) {
            //     return $user->role ? $user->role->role_name : '-';
            // })
            ->addColumn('aksi', function ($agen) { // menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/edit") . '\')" class="btn btn-info"><i class="fas fa-edit"></i> Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url("agen/$agen->agen_id/delete").'\')" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah HTML
            ->make(true);
    }

}
