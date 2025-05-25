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

    public function create()
    {
        $agen = Agen::all();
        $barang = Barang::all();

        return view('transaksi.create', compact('agen', 'barang'));
    
        // $jenisEvent = Agen::select('jenis_event_id', 'jenis_event_name')->get();
        // $user = Barang::select('user_id', 'name')->where('role_id', 3)->get();
        // $jabatan = Position::select('jabatan_id', 'jabatan_name')->get();
        // return view('admin.event.create_ajax')
        //     ->with('jenisEvent', $jenisEvent)
        //     ->with('user', $user)
        //     ->with('jabatan', $jabatan);
    }

    // public function store(Request $request)
    // {
    //     // Cek apakah request berupa ajax atau ingin JSON
    //     if ($request->ajax() || $request->wantsJson()) {
    //         // Aturan validasi
    //         $rules = [
    //             'event_name' => 'required|string|max:100',
    //             'event_code' => 'required|string|max:10|unique:m_event,event_code',
    //             'event_description' => 'required|string',
    //             'start_date' => 'required|date',
    //             'end_date' => 'required|date|after_or_equal:start_date',
    //             'jenis_event_id' => 'required|integer',
    //             'point' => 'required|numeric|min:0',

    //             // Validasi untuk array user_id dan jabatan_id
    //             'participant' => 'required|array|min:1',
    //             'participant.*.user_id' => 'required|integer|exists:m_user,user_id',
    //             'participant.*.jabatan_id' => 'required|integer|exists:m_jabatan,jabatan_id',
    //         ];

    //         // Gunakan Validator untuk memvalidasi data
    //         $validator = Validator::make($request->all(), $rules);

    //         // Jika validasi gagal
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validasi Gagal',
    //                 'msgField' => $validator->errors(),
    //             ]);
    //         }

    //         try {
    //             // Simpan data event
    //             $event = Event::create([
    //                 'event_name' => $request->input('event_name'),
    //                 'event_code' => $request->input('event_code'),
    //                 'event_description' => $request->input('event_description'),
    //                 'start_date' => $request->input('start_date'),
    //                 'end_date' => $request->input('end_date'),
    //                 'jenis_event_id' => $request->input('jenis_event_id'),
    //                 'status' => 'not started', // Tambahkan status default
    //                 'point' => $request->input('point'),
    //             ]);

    //             // Simpan data ke event_participants untuk setiap kombinasi user_id dan jabatan_id

    //             foreach ($request->participant as $participants) {
    //                 EventParticipant::create([
    //                     'event_id' => $event->event_id, // Ambil ID dari event yang baru disimpan
    //                     'user_id' => $participants['user_id'],
    //                     'jabatan_id' => $participants['jabatan_id']
    //                 ]);
    //             }

    //             $eventParticipants = EventParticipant::where('event_id', $event->event_id)->get();
    //             $users  = User::whereIn('user_id', $eventParticipants->pluck('user_id'))->get();
    //             Notification::send($users, new EventNotification($event));

    //             $pimpinan = User::where('role_id', 2)->get();
    //             Notification::send($pimpinan, new PimpinanNotification($event));

    //             // Jika berhasil
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Data event berhasil disimpan',
    //             ]);
    //         } catch (\Exception $e) {
    //             // Jika terjadi kesalahan pada proses simpan
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
    //             ]);
    //         }
    //     }

    //     // Redirect jika bukan request Ajax
    //     return redirect('/');
    // }

}
