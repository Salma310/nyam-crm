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
use Illuminate\Support\Facades\DB;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
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
        $transaksi = Transaksi::with(['agen', 'detailTransaksi'])
            ->orderByDesc('tgl_transaksi'); // <-- urutkan dari yang terbaru

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

    public function create()
    {
        $barang = Barang::select('barang_id', 'kode_barang', 'nama_barang', 'hpp', 'stok')->get();
        $agen = Agen::all();

        foreach ($barang as $b) {
            $kode = str_pad($b->kode_barang, 10);
            $nama = str_pad($b->nama_barang, 25);
            $hpp = str_pad('Rp' . number_format($b->hpp, 0, ',', '.'), 12, ' ', STR_PAD_LEFT);
            $stok = str_pad('Stok: ' . $b->stok, 10, ' ', STR_PAD_LEFT);
            $b->label = $kode . $nama . $hpp . $stok;
        }

        return view('transaksi.create', compact('barang', 'agen'));
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'agen_id' => 'required|exists:m_agen,agen_id',
                'diskon_transaksi' => 'nullable|numeric|min:0',
                'pajak_transaksi' => 'nullable|numeric|min:0',
                'barang' => 'required|array|min:1',
                'barang.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
                'barang.*.qty' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            DB::beginTransaction();

            try {
                $last = Transaksi::latest('transaksi_id')->first();
                $nextId = $last ? $last->transaksi_id + 1 : 1;
                $kode = 'TRX' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

                $diskon = $request->diskon_transaksi ?? 0;
                $pajak = $request->pajak_transaksi ?? 0;
                $totalHarga = 0;

                foreach ($request->barang as $item) {
                    $hargaAgen = HargaAgen::where('agen_id', $request->agen_id)
                        ->where('barang_id', $item['barang_id'])
                        ->first();
                    $harga = $hargaAgen ? $hargaAgen->harga : Barang::find($item['barang_id'])->hpp;
                    $totalHarga += $harga * $item['qty'];
                }

                $hargaAkhir = $totalHarga - $diskon + $pajak;

                $transaksi = Transaksi::create([
                    'kode_transaksi' => $kode,
                    'agen_id' => $request->agen_id,
                    'diskon_transaksi' => $diskon,
                    'pajak_transaksi' => $pajak,
                    'harga_total' => $hargaAkhir,
                    'tgl_transaksi' => now(),
                ]);

                foreach ($request->barang as $item) {
                    $hargaAgen = HargaAgen::where('agen_id', $request->agen_id)
                        ->where('barang_id', $item['barang_id'])
                        ->first();

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->transaksi_id,
                        'barang_id' => $item['barang_id'],
                        'qty' => $item['qty'],
                        'harga_agen_id' => optional($hargaAgen)->harga_agen_id,
                    ]);

                    $barang = Barang::findOrFail($item['barang_id']);
                    $barang->stok -= $item['qty'];
                    $barang->save();
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Transaksi berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/');
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

    public function sendInvoiceByEmail($id)
    {
        try {
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
            ]);

            $fileName = 'invoice-' . $transaksi->kode_transaksi . '.pdf';
            $filePath = storage_path('app/temp/' . $fileName);
            $pdf->save($filePath);

            Mail::to($transaksi->agen->email)->send(new InvoiceMail($filePath, $fileName));

            return response()->json(['message' => 'Email berhasil dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    public function sendInvoiceToWablas($id)
    {
        try {
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
            $publicDir = public_path('storage/temp');

            if (!file_exists($publicDir)) {
                mkdir($publicDir, 0775, true);
            }

            $filePath = $publicDir . '/' . $fileName;

            PDF::loadView('transaksi.invoice', [
                'transaksi' => $transaksi,
                'hargaAgenMap' => $hargaAgenMap,
                'subtotal' => $subtotal,
                'Gtotal' => $grandTotal
            ])->save($filePath);

            $fileUrl = asset('storage/temp/' . $fileName);
            $nomorWhatsapp = $transaksi->agen->no_telf;

            $payload = [
                'data' => [
                    [
                        'phone' => $nomorWhatsapp,
                        'document' => $fileUrl,
                        'filename' => $fileName
                    ]
                ]
            ];

            $token = "GNZMk9TteQJj9ooLoPCuAF898KDaJTbeagVdNpvYDVsMOJq2SgHWSBXQsVHZ41kM";
            $secret_key = "ULyzAU93";

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://texas.wablas.com/api/v2/send-document",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: $token.$secret_key",
                    "Content-Type: application/json"
                ],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($response === false || $httpCode !== 200) {
                return response()->json([
                    'message' => 'Gagal mengirim WhatsApp.',
                    'error' => $curlError ?: $response
                ], 500);
            }

            $resultData = json_decode($response, true);

            // Jika dari Wablas tidak sukses
            if (!isset($resultData['status']) || $resultData['status'] !== true) {
                return response()->json([
                    'message' => 'Gagal mengirim melalui WhatsApp.',
                    'error' => $resultData['message'] ?? 'Tidak diketahui'
                ], 500);
            }

            return response()->json([
                'message' => 'Invoice berhasil dikirim melalui WhatsApp.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending invoice via Wablas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengirim invoice via WhatsApp.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
