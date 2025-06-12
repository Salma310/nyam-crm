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
use Illuminate\Support\Facades\Http;

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
                $btn = '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/show") . '\')" class="btn btn-outline-primary"><i class="fas fa-qrcode"></i> Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/edit") . '\')" class="btn btn-outline-info"><i class="fas fa-edit"></i> Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url("agen/$agen->agen_id/delete") . '\')" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah HTML
            ->make(true);
    }

    public function create()
    {
        return view('agen.add');
    }

    public function store(Request $request)
    {
        //cek apsakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
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

            if ($validator->fails()) {
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

    public function show($id)
    {
        // Ambil data agen dengan relasi hargaAgen dan transaksi + detail transaksi + barang
        $agen = Agen::with([
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
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'harga' => 'required|numeric|min:0',
                'diskon' => 'required|numeric|min:0',
                'diskon_persen' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = HargaAgen::find($id);

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
    }

    public function edit($id)
    {
        $agen = Agen::findOrFail($id);
        return view('agen.edit', compact('agen'));
    }

    public function update(Request $request, $id)
    {
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

    public function confirm(string $agen_id)
    {
        $agen = Agen::find($agen_id);
        return view('agen.confirm', ['agen' => $agen],);
    }

    public function delete($id)
    {
        $agen = Agen::findOrFail($id);

        // Cek apakah agen memiliki relasi dengan transaksi atau harga_agen
        $relatedTransaksi = $agen->transaksi()->exists();
        $relatedHarga = $agen->hargaAgen()->exists();

        if ($relatedTransaksi || $relatedHarga) {
            return response()->json([
                'status' => false,
                'message' => 'Data agen tidak bisa dihapus karena masih terhubung dengan data transaksi atau harga produk.'
            ]);
        }

        $agen->delete();

        return response()->json([
            'status' => true,
            'message' => 'Agen berhasil dihapus.'
        ]);
    }

    public function forceDelete($id)
    {
        $agen = Agen::findOrFail($id);

        try {
            DB::transaction(function () use ($agen) {
                $agen->transaksi()->delete();      // hapus semua transaksi terkait
                $agen->hargaAgen()->delete();      // hapus semua harga agen terkait
                $agen->delete();                   // hapus agen
            });

            return response()->json([
                'status' => true,
                'message' => 'Agen beserta data terkait berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus agen: ' . $e->getMessage()
            ]);
        }
    }

    public function sendReminder($id)
    {
        $agen = Agen::findOrFail($id);

        $phone = $agen->no_telf; // pastikan ini format internasional, contoh: 628123xxxxx
        $caption = "Halo *{$agen->nama}* 👋\nKami dari *Nyam Baby Food* ingin mengingatkan bahwa Anda belum melakukan transaksi dalam 30 hari terakhir. Yuk segera restock kebutuhan pelanggan Anda bersama kami! 🍲💼\n\nJika butuh bantuan atau informasi lebih lanjut, silakan hubungi kami.\n\nTerima kasih 😊";

        $response = $this->sendTextWithWhapi($phone, $caption);

        if ($response->successful()) {
            // return back()->with('success', 'Pengingat berhasil dikirim ke WhatsApp.');
            return response()->json([
                'message' => 'Reminder berhasil dikirim ke WhatsApp untuk agen: ' . $agen->nama
            ]);
        } else {
            // return back()->with('error', 'Gagal mengirim pengingat: ' . $response->body());
            return response()->json([
                'response' => $response->json()
                // 'error' => 'Gagal mengirim pengingat.'
            ], 500);
        }
    }

    private function sendTextWithWhapi($phone, $message)
    {
        $url = "https://gate.whapi.cloud/messages/text";

        return Http::withHeaders([
            'Authorization' => 'Bearer klhIQczhtqcLcLeyI7XlvO6OWvB5yRc6',
        ])->post($url, [
            'to' => $phone . '@s.whatsapp.net',
            'body' => $message
        ]);
    }
}
