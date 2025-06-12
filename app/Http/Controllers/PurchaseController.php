<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Purchase;
use App\Models\Barang;
use App\Models\DetailPurchase;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PurchaseController extends Controller
{
    //
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Agen',
            'list' => ['Home', 'Agen']
        ];

        $title = 'purchase';
        $purchase = Purchase::all();
        $barang = Barang::all();
        $detailPurchase = DetailPurchase::all();

        $activeMenu = 'purchase';

        return view('purchase.index', [
            'title' => $title,
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'purchase' => $purchase,
            'barang' => $barang,
            'detailPurchase' => $detailPurchase
        ]);
    }

    public function list(Request $request)
    {
        $purchase = Purchase::with(['detailPurchase', 'detailPurchase.barang'])->orderByDesc('tgl_transaksi');;

         if ($request->filled('start_date') && $request->filled('end_date')) {
            $purchase->whereBetween('tgl_transaksi', [$request->start_date, $request->end_date]);
        } else {
            // Default ke transaksi bulan ini
            $purchase->whereBetween('tgl_transaksi', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ]);
        }

        return DataTables::of($purchase)
            ->addIndexColumn()
            ->addColumn('nama_barang', function ($t) {
                return $t->detailPurchase->map(function ($detail) {
                    return $detail->barang->nama_barang ?? '-';
                })->implode(', ');
            })
            ->addColumn('total_qty', function ($t) {
                return $t->detailPurchase->sum('qty');
            })
            ->addColumn('aksi', function ($t) {
                $btn = '<button onclick="modalAction(\'' . url("purchase/$t->transaksi_masuk_id/show") . '\')" class="btn btn-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show($id)
    {
        // Ambil transaksi lengkap dengan relasi agen, detail, dan barang
        $purchase = Purchase::with(['detailPurchase', 'detailPurchase.barang'])->findOrFail($id);

        $totalHarga = 0;

        $detailHarga = [];

        foreach ($purchase->detailPurchase as $detail) {
            $barangId = $detail->barang_id;

            $barang = Barang::where('barang_id', $barangId)->first();

            if ($barang) {
                $hargaSatuan = $detail->barang->hpp;
                $diskon = $detail->diskon;
                $pajak = $detail->pajak;

                $subtotal = $hargaSatuan * $detail->qty;
                $detailHarga[] = [
                    'detail' => $detail,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                    'hpp' => $detail->barang->hpp ?? 0,
                ];
            }
        }

        return view('purchase.detail', compact('purchase', 'detailHarga'));
    }

    public function create()
    {
        $barang = Barang::select('barang_id', 'kode_barang', 'nama_barang', 'hpp', 'stok')->get();
        foreach ($barang as $b) {
            $kode      = str_pad($b->kode_barang, 10); // lebar 10 karakter
            $nama      = str_pad($b->nama_barang, 25); // lebar 25 karakter
            $hpp       = str_pad('Rp' . number_format($b->hpp, 0, ',', '.'), 12, ' ', STR_PAD_LEFT);
            $stok      = str_pad('Stok: ' . $b->stok, 10, ' ', STR_PAD_LEFT);

            $b->label = $kode . $nama . $hpp . $stok;
        }
        return view('purchase.add')
            ->with('barang', $barang);
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $rules = [
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
                // Auto generate kode
                $last = Purchase::latest('transaksi_masuk_id')->first();
                $nextId = $last ? $last->transaksi_masuk_id + 1 : 1;
                $kode = 'TRXMSK' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

                $diskon = $request->diskon_transaksi ?? 0;
                $pajak = $request->pajak_transaksi ?? 0;
                $totalHarga = 0;

                foreach ($request->barang as $item) {
                    $barang = Barang::findOrFail($item['barang_id']);
                    $totalHarga += $barang->hpp * $item['qty'];
                }

                $hargaAkhir = $totalHarga - $diskon + $pajak;

                $purchase = Purchase::create([
                    'kode_transaksi_masuk' => $kode,
                    'diskon_transaksi' => $diskon,
                    'pajak_transaksi' => $pajak,
                    'harga_total' => $hargaAkhir,
                    'tgl_transaksi' => now(),
                ]);

                foreach ($request->barang as $item) {
                    DetailPurchase::create([
                        'transaksi_masuk_id' => $purchase->transaksi_masuk_id,
                        'barang_id' => $item['barang_id'],
                        'qty' => $item['qty'],
                    ]);

                    // Tambah stok
                    $barang = Barang::findOrFail($item['barang_id']);
                    $barang->stok += $item['qty'];
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
}
