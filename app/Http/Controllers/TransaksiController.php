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
use App\Models\HargaAgen;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


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

        return view('transaksi.index', [
            'title' => $title,
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'agen' => $agen,
            'transaksi' => $transaksi,
            'barang' => $barang,
            'detailTransaksi' => $detailTransaksi
        ]);
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
        // Ambil transaksi lengkap dengan relasi agen, detail, dan barang
        $transaksi = Transaksi::with(['agen', 'detailTransaksi.barang'])->findOrFail($id);
        $agenId = $transaksi->agen_id;

        $totalHarga = 0;

        $detailHarga = [];

        foreach ($transaksi->detailTransaksi as $detail) {
            $barangId = $detail->barang_id;

            $hargaAgen = HargaAgen::where('agen_id', $agenId)
                ->where('barang_id', $barangId)
                ->first();

            if ($hargaAgen) {
                $hargaSatuan = $hargaAgen->harga;
                $diskon = $hargaAgen->diskon + (($hargaSatuan * $hargaAgen->diskon_persen) / 100);

                $hargaSetelahDiskon = $hargaSatuan - $diskon;
                $hargaFinal = $hargaSetelahDiskon * $detail->qty;

                $totalHarga += $hargaFinal;

                $detailHarga[] = [
                    'detail' => $detail,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'harga_final' => $hargaFinal,
                    'hpp' => $detail->barang->hpp ?? 0,
                ];
            }
        }

        return view('transaksi.detail', compact('transaksi', 'totalHarga', 'detailHarga'));
    }

    public function printInvoice($id)
    {
        $transaksi = Transaksi::with(['agen', 'detailTransaksi.barang'])->findOrFail($id);
        $agenId = $transaksi->agen_id;

        $hargaAgenMap = [];
        $subtotal = 0;

        foreach ($transaksi->detailTransaksi as $detail) {
            $barangId = $detail->barang_id;

            $hargaAgen = HargaAgen::where('agen_id', $agenId)
                ->where('barang_id', $barangId)
                ->first();

            if ($hargaAgen) {
                $hargaSatuan = $hargaAgen->harga;
                $diskon = $hargaAgen->diskon + (($hargaSatuan * $hargaAgen->diskon_persen) / 100);

                $hargaSetelahDiskon = $hargaSatuan - $diskon;
                $hargaFinal = $hargaSetelahDiskon * $detail->qty;

                $totalDiskonItem = $diskon * $detail->qty;

                $subtotal += $hargaFinal;

                $hargaAgenMap[$barangId] = [
                    'harga_satuan' => $hargaSatuan,
                    'totalDiskonItem' => $totalDiskonItem,
                    'harga_setelah_diskon' => $hargaSetelahDiskon,
                    'harga_final' => $hargaFinal
                ];
            }
        }

        $grandTotal = $subtotal - ($transaksi->diskon ?? 0) + ($transaksi->pajak_transaksi ?? 0);

        $pdf = PDF::loadView('transaksi.invoice', [
            'transaksi' => $transaksi,
            'hargaAgenMap' => $hargaAgenMap,
            'subtotal' => $subtotal,
            'Gtotal' => $grandTotal
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('invoice-' . $transaksi->kode_transaksi . '.pdf');
    }

    public function sendInvoiceToWablas($id)
    {
        $transaksi = Transaksi::with(['agen', 'detailTransaksi.barang'])->findOrFail($id);
        $agenId = $transaksi->agen_id;

        $hargaAgenMap = [];
        $subtotal = 0;

        foreach ($transaksi->detailTransaksi as $detail) {
            $barangId = $detail->barang_id;

            $hargaAgen = HargaAgen::where('agen_id', $agenId)
                ->where('barang_id', $barangId)
                ->first();

            if ($hargaAgen) {
                $hargaSatuan = $hargaAgen->harga;
                $diskon = $hargaAgen->diskon + (($hargaSatuan * $hargaAgen->diskon_persen) / 100);

                $hargaSetelahDiskon = $hargaSatuan - $diskon;
                $hargaFinal = $hargaSetelahDiskon * $detail->qty;

                $totalDiskonItem = $diskon * $detail->qty;

                $subtotal += $hargaFinal;

                $hargaAgenMap[$barangId] = [
                    'harga_satuan' => $hargaSatuan,
                    'totalDiskonItem' => $totalDiskonItem,
                    'harga_setelah_diskon' => $hargaSetelahDiskon,
                    'harga_final' => $hargaFinal
                ];
            }
        }

        $grandTotal = $subtotal - ($transaksi->diskon ?? 0) + ($transaksi->pajak_transaksi ?? 0);

        $fileName = 'invoice-' . $transaksi->kode_transaksi . '.pdf';
        $filePath = storage_path('app/temp/' . $fileName);

        // Generate PDF dan simpan
        PDF::loadView('transaksi.invoice', [
            'transaksi' => $transaksi,
            'hargaAgenMap' => $hargaAgenMap,
            'subtotal' => $subtotal,
            'Gtotal' => $grandTotal
        ])->save($filePath);

        // Baca file dan encode base64
        $fileContent = file_get_contents($filePath);
        $fileBase64 = base64_encode($fileContent);

        $curl = curl_init();
        $token = "5IO4MTcMD6q0i6vwYGliL4QmSumw66ORBECk7YvknKhKErhAHhy6D8d";
        $secret_key = "2U28NYOp";

        // Ambil nomor agen
        $nomorWhatsapp = $transaksi->agen->no_telf; // Pastikan sudah dalam format internasional (628xxx)

        $data = [
            'phone' => $nomorWhatsapp,
            'file' => $fileBase64,
            'filename' => $fileName,
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wablas.com/api/send-document-from-local",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: $token.$secret_key",
            ],
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        $result = curl_exec($curl);
        curl_close($curl);

        echo "<pre>";
        print_r($result);
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
