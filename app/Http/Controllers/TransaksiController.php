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
        $query = Transaksi::with(['agen', 'detailTransaksi'])
            ->orderByDesc('tgl_transaksi');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_transaksi', [$request->start_date, $request->end_date]);
        } else {
            // Default ke transaksi bulan ini
            $query->whereBetween('tgl_transaksi', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ]);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_agen', fn($t) => $t->agen->nama ?? '-')
            ->addColumn('total_qty', fn($t) => $t->detailTransaksi->sum('qty'))
            ->addColumn('aksi', fn($t) =>
                '<button onclick="modalAction(\'' . url("transaksi/$t->transaksi_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button>')
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // public function list(Request $request)
    // {
    //     $transaksi = Transaksi::with(['agen', 'detailTransaksi'])
    //         ->orderByDesc('tgl_transaksi'); // <-- urutkan dari yang terbaru

    //     return DataTables::of($transaksi)
    //         ->addIndexColumn()
    //         ->addColumn('nama_agen', function ($t) {
    //             return $t->agen->nama ?? '-'; // atau ->nama, sesuai nama kolom kamu
    //         })
    //         ->addColumn('total_qty', function ($t) {
    //             return $t->detailTransaksi->sum('qty');
    //         })
    //         ->addColumn('aksi', function ($t) {
    //             $btn = '<button onclick="modalAction(\'' . url("transaksi/$t->transaksi_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi'])
    //         ->make(true);
    // }

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

    public function sendInvoiceToWhapi($id)
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

        // Generate PDF invoice
        $fileName = 'invoice-' . $transaksi->kode_transaksi . '.pdf';
        $filePath = storage_path('app/public/invoices/' . $fileName);

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        PDF::loadView('transaksi.invoice', [
            'transaksi' => $transaksi,
            'hargaAgenMap' => $hargaAgenMap,
            'subtotal' => $subtotal,
            'Gtotal' => $grandTotal
        ])->save($filePath);

        // Kirim ke WhatsApp
        return $this->sendPdfWithWhapi($transaksi->agen->no_telf, $fileName, "Hai! 👋\n\nTerima kasih telah berbelanja di *Nyam Baby Food* 🍽️✨\nBerikut kami lampirkan invoice resmi untuk pesanan Anda.\n\n📌 Mohon cek kembali detail transaksi. Jika ada pertanyaan, tim kami siap membantu.\n\nSemoga hari Anda menyenangkan & tetap sehat selalu! 🌿😊\n#NyamBabyFood");
    }

    private function sendPdfWithWhapi($phone, $fileName, $caption = null)
    {
        $url = "https://gate.whapi.cloud/messages/document";

        $filePath = storage_path('app/public/invoices/' . $fileName);

        if (!file_exists($filePath)) {
            return response()->json([
                'message' => 'File tidak ditemukan.',
                'error' => 'File not found: ' . $filePath
            ], 404);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer klhIQczhtqcLcLeyI7XlvO6OWvB5yRc6',
        ])->attach(
            'media',
            file_get_contents($filePath),
            $fileName
        )->post($url, [
            'to' => $phone . '@s.whatsapp.net',
            'fileName' => $fileName,
            'caption' => $caption ?? 'Berikut adalah invoice pesanan Anda.'
        ]);

        if ($response->successful()) {
            return response()->json([
                'message' => 'Invoice berhasil dikirim ke WhatsApp.',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Gagal mengirim WhatsApp.',
                'error' => $response->body()
            ], 500);
        }
    }
}
