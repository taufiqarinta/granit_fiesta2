<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDailyActivityRequest;
use App\Models\DailyActivity;
use App\Models\Depaterment;
use App\Models\Wilayah;
use App\Models\P1;
use App\Models\P2;
use App\Models\Project;
use App\Models\SubProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\FormLkp;
use App\Models\Ukuran;
use App\Models\Merk;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KomplainExport;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;





class KomplainController extends Controller
{
    public function indexKomplain(Request $request)
    {
        $query = FormLkp::with(['merks', 'ukurans', 'motifs']);

        // $allowedNames = ['QMS', 'SLS', 'DIR', 'GM'];
        $currentName = auth()->user()->id_customer;

        // if (!in_array($currentName, $allowedNames)) {
        //     $query->where('created_by', $currentName);
        // }

        $currentRole = auth()->user()->role_as;

        if ($currentRole == 0) {
            $query->where('created_by', $currentName);
        }

        // Filter tanggal jika ada input
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_date', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_date', '<=', $request->tanggal_akhir);
        }

        $formLkps = $query->orderBy('id', 'desc')->paginate(10);

        $provCodes = $formLkps->pluck('provinsi')->unique();
        $kabCodes = $formLkps->pluck('kabupaten')->unique();

        $merkIds = $formLkps->pluck('id_merks')->unique()->filter();
        $ukuranIds = $formLkps->pluck('id_ukurans')->unique()->filter();
        $motifIds = $formLkps->pluck('id_motifs')->unique()->filter();

        $merks = \App\Models\Merk::whereIn('id', $merkIds)->pluck('name', 'id');
        $ukurans = \App\Models\Ukuran::whereIn('id', $ukuranIds)->pluck('name', 'id');
        $motifs = \App\Models\Motif::whereIn('id', $motifIds)->pluck('name', 'id');

        $wilayahList = collect();

        foreach ($provCodes as $kodeProv) {
            $wilayah = \App\Models\Wilayah::where('kode', 'LIKE', "{$kodeProv}%")->first();
            if ($wilayah) {
                $wilayahList->put($kodeProv, $wilayah->nama);
            }
        }

        foreach ($kabCodes as $kodeKab) {
            $wilayah = \App\Models\Wilayah::where('kode', 'LIKE', "{$kodeKab}%")->first();
            if ($wilayah) {
                $wilayahList->put($kodeKab, $wilayah->nama);
            }
        }

        return view('komplain.index', compact('formLkps', 'merks', 'ukurans', 'motifs', 'wilayahList'));
    }


    public function createKomplain()
    {
        $idCust = auth()->user()->id_customer;
        
        $prefix = 'LKP' . date('ym');

        $lastNomor = FormLKP::where('nomor', 'like', $prefix . '%')
            ->orderByDesc('nomor')
            ->value('nomor');

        if ($lastNomor) {
            $lastNumberInt = (int)substr($lastNomor, -3);
            $newNumber = $lastNumberInt + 1;
        } else {
            $newNumber = 1;
        }

        $nomor = $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $merks = Merk::select('merks.id', 'merks.name')
        ->join('users_merks', 'users_merks.id_merks', '=', 'merks.id')
        ->where('users_merks.id_customer', $idCust)
        ->orderBy('merks.name')
        ->get();

        $ukurans = Ukuran::select('id', 'name')->get();

        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')
        ->get();

        return view('komplain.create', compact('nomor','merks','ukurans', 'provinsis'));
    }





    // public function storeKomplain(Request $request)
    // {
    //     // dd($request->file('lampiran_bukti'));

    //     try {
    //         // dd($request->all());
    //         $validated = $request->validate([
    //             'nomor' => 'required',
    //             'tanggal' => 'required|date',
    //             'nama' => 'required',
    //             'email' => 'required|email',
    //             'provinsi' => 'required',
    //             'kabupaten' => 'required',
    //             'via_agen' => 'required',
    //             'no_sj' => 'required',
    //             'lampiran_sj' => 'required',
    //             'tanggal_pembelian' => 'required|date',
    //             'tanggal_komplain' => 'required|date',
    //             'sales' => 'required',
    //             'id_merks' => 'required',
    //             'id_ukurans' => 'required',
    //             'id_motifs' => 'required',
    //             'kw' => 'required',
    //             'tonality' => 'required|numeric',
    //             'kaliber' => 'required',
    //             'batch' => 'required',
    //             'lampiran_batch' => 'required',
    //             'jumlah_order' => 'required|numeric',
    //             'jumlah_kirim' => 'required|numeric',
    //             'jumlah_komplain' => 'required|numeric',
    //             'jenis_komplain' => 'required',
    //             'lampiran_bukti' => 'required|array',
    //             'lampiran_bukti.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
    //             'penyelesaian' => 'required',
    //         ]);

    //         // Upload lampiran SJ
    //         $lampiranSJPath = $request->file('lampiran_sj')->store('lampiran_sj', 'public');

    //         // Upload lampiran batch
    //         $lampiranBatchPath = $request->file('lampiran_batch')->store('lampiran_batch', 'public');

    //         // Upload lampiran bukti (multiple files)
    //         // $lampiranBuktiPaths = [];
    //         // if ($request->hasFile('lampiran_bukti')) {
    //         //     foreach ($request->file('lampiran_bukti') as $file) {
    //         //         $lampiranBuktiPaths[] = $file->store('lampiran_bukti', 'public');
    //         //     }
    //         // }

    //         // Simpan data ke database
    //         $formLkp = FormLkp::create([
    //             'nomor' => $validated['nomor'],
    //             'tanggal' => $validated['tanggal'],
    //             'nama' => $validated['nama'],
    //             'email' => $validated['email'],
    //             'provinsi' => $validated['provinsi'],
    //             'kabupaten' => $validated['kabupaten'],
    //             'via_agen' => $validated['via_agen'],
    //             'no_sj' => $validated['no_sj'],
    //             'lampiran_sj' => $lampiranSJPath,
    //             'tanggal_pembelian' => $validated['tanggal_pembelian'],
    //             'tanggal_komplain' => $validated['tanggal_komplain'],
    //             'sales' => $validated['sales'],
    //             'id_merks' => $validated['id_merks'],
    //             'id_ukurans' => $validated['id_ukurans'],
    //             'id_motifs' => $validated['id_motifs'],
    //             'kw' => $validated['kw'],
    //             'tonality' => $validated['tonality'],
    //             'kaliber' => $validated['kaliber'],
    //             'batch' => $validated['batch'],
    //             'lampiran_batch' => $lampiranBatchPath,
    //             'jumlah_order' => $validated['jumlah_order'],
    //             'jumlah_kirim' => $validated['jumlah_kirim'],
    //             'jumlah_komplain' => $validated['jumlah_komplain'],
    //             'jenis_komplain' => $validated['jenis_komplain'],
    //             // 'lampiran_bukti' => json_encode($lampiranBuktiPaths),
    //             'penyelesaian' => $validated['penyelesaian'],
    //             'created_by' => auth()->user()->name,


    //             'approval1_by' => "QMS",
    //             'approval2_by' => "GM",
    //             'approval3_by' => "SLS",
    //             'approval4_by' => "DIR",

    //             'created_date' => now()->toDateString(),
    //             'created_time' => now()->format('H:i:s'),

    //             'approval1_date' => now()->toDateString(),
    //             'approval2_date' => now()->toDateString(),
    //             'approval3_date' => now()->toDateString(),
    //             'approval4_date' => now()->toDateString(),

    //             'approval1_time' => now()->format('H:i:s'),
    //             'approval2_time' => now()->format('H:i:s'),
    //             'approval3_time' => now()->format('H:i:s'),
    //             'approval4_time' => now()->format('H:i:s'),

    //             // 'approval1_date' => $request->approval1_date,
    //             // 'approval2_date' => $request->approval2_date,
    //             // 'approval3_date' => $request->approval3_date,
    //             // 'approval4_date' => $request->approval4_date,

    //             // 'approval1_time' => $request->approval1_time,
    //             // 'approval2_time' => $request->approval2_time,
    //             // 'approval3_time' => $request->approval3_time,
    //             // 'approval4_time' => $request->approval4_time,

    //             'approval1_status' => 0,
    //             'approval2_status' => 0,
    //             'approval3_status' => 0,
    //             'approval4_status' => 0,

    //             'status' => 0,
    //             'flag' => 0,
    //         ]);

    //         // Simpan lampiran bukti ke tabel terpisah
    //         foreach ($request->file('lampiran_bukti') as $file) {
    //             $path = $file->store('lampiran_bukti', 'public');

    //             DB::table('form_lkp_lampiran_bukti')->insert([
    //                 'nomor' => $formLkp->nomor,
    //                 'lampiran' => $path,
    //             ]);
    //         }

    //     } catch (Exception $e) {
    //         // Log error ke file log Laravel
    //         Log::error('Gagal menyimpan komplain: '.$e->getMessage());

    //         // Kembalikan ke halaman sebelumnya dengan pesan error
    //         return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
    //     }

    //     return redirect()->route('komplain.index')->with('success', 'Komplain berhasil disimpan.');
    // }

    public function storeKomplain(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor' => 'required',
                'tanggal' => 'required|date',
                'nama' => 'required',
                'email' => 'required|email',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'via_agen' => 'required',
                'no_sj' => 'required',
                'lampiran_sj' => 'required|file',
                'tanggal_pembelian' => 'required|date',
                'tanggal_komplain' => 'required|date',
                'sales' => 'required',
                'id_merks' => 'required',
                'id_ukurans' => 'required',
                'id_motifs' => 'required',
                'kw' => 'required',
                'tonality' => 'required|numeric',
                'kaliber' => 'required',
                'batch' => 'required',
                'lampiran_batch' => 'required|file',
                'jumlah_order' => 'required|numeric',
                'jumlah_kirim' => 'required|numeric',
                'jumlah_komplain' => 'required|numeric',
                'jenis_komplain' => 'required',
                'lampiran_bukti' => 'required|array',
                'lampiran_bukti.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                'penyelesaian' => 'required',
            ]);

            // === Simpan Lampiran SJ ===
            $lampiranSJPath = null;
            if ($request->hasFile('lampiran_sj')) {
                $fileSJ = $request->file('lampiran_sj');
                $namaFileSJ = $validated['nomor'] . '_sj_' . time() . '_' . Str::random(5) . '.' . $fileSJ->getClientOriginalExtension();

                $tujuanFolderSJ = public_path('lampiran_sj');
                if (!file_exists($tujuanFolderSJ)) {
                    mkdir($tujuanFolderSJ, 0755, true);
                }

                $fileSJ->move($tujuanFolderSJ, $namaFileSJ);
                $lampiranSJPath = 'lampiran_sj/' . $namaFileSJ;
            }

            // === Simpan Lampiran Batch ===
            $lampiranBatchPath = null;
            if ($request->hasFile('lampiran_batch')) {
                $fileBatch = $request->file('lampiran_batch');
                $namaFileBatch = $validated['nomor'] . '_batch_' . time() . '_' . Str::random(5) . '.' . $fileBatch->getClientOriginalExtension();

                $tujuanFolderBatch = public_path('lampiran_batch');
                if (!file_exists($tujuanFolderBatch)) {
                    mkdir($tujuanFolderBatch, 0755, true);
                }

                $fileBatch->move($tujuanFolderBatch, $namaFileBatch);
                $lampiranBatchPath = 'lampiran_batch/' . $namaFileBatch;
            }


            // Simpan data utama ke form_lkp
            $formLkp = FormLkp::create([
                'nomor' => $validated['nomor'],
                'tanggal' => $validated['tanggal'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'provinsi' => $validated['provinsi'],
                'kabupaten' => $validated['kabupaten'],
                'via_agen' => $validated['via_agen'],
                'no_sj' => $validated['no_sj'],
                'lampiran_sj' => $lampiranSJPath,
                'tanggal_pembelian' => $validated['tanggal_pembelian'],
                'tanggal_komplain' => $validated['tanggal_komplain'],
                'sales' => $validated['sales'],
                'id_merks' => $validated['id_merks'],
                'id_ukurans' => $validated['id_ukurans'],
                'id_motifs' => $validated['id_motifs'],
                'kw' => $validated['kw'],
                'tonality' => $validated['tonality'],
                'kaliber' => $validated['kaliber'],
                'batch' => $validated['batch'],
                'lampiran_batch' => $lampiranBatchPath,
                'jumlah_order' => $validated['jumlah_order'],
                'jumlah_kirim' => $validated['jumlah_kirim'],
                'jumlah_komplain' => $validated['jumlah_komplain'],
                'jenis_komplain' => $validated['jenis_komplain'],
                'penyelesaian' => $validated['penyelesaian'],
                'created_by' => auth()->user()->id_customer,
                'approval1_by' => "QMS",
                'approval2_by' => "GM",
                'approval3_by' => "SLS",
                'approval4_by' => "DIR",
                'created_date' => now()->toDateString(),
                'created_time' => now()->format('H:i:s'),
                'approval1_date' => now()->toDateString(),
                'approval2_date' => now()->toDateString(),
                'approval3_date' => now()->toDateString(),
                'approval4_date' => now()->toDateString(),
                'approval1_time' => now()->format('H:i:s'),
                'approval2_time' => now()->format('H:i:s'),
                'approval3_time' => now()->format('H:i:s'),
                'approval4_time' => now()->format('H:i:s'),
                'approval1_status' => 0,
                'approval2_status' => 0,
                'approval3_status' => 0,
                'approval4_status' => 0,
                'status' => 0,
                'flag' => 0,
            ]);

            // Simpan lampiran bukti ke tabel terpisah
            if ($request->hasFile('lampiran_bukti')) {
                foreach ($request->file('lampiran_bukti') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $sanitizedName = str_replace(' ', '_', $originalName);

                    $namaFile = $formLkp->nomor . '_bukti_' . time() . '_' . Str::random(13) . $sanitizedName . '.' . $extension;


                    $file->move(public_path('lampiran_bukti'), $namaFile);

                    DB::table('form_lkp_lampiran_bukti')->insert([
                        'nomor' => $formLkp->nomor,
                        'lampiran' => $namaFile,
                    ]);
                }
            }

            // Ambil data tambahan
            $provinsiText = DB::table('wilayah')->where('kode', $validated['provinsi'])->value('nama');
            $kabupatenText = DB::table('wilayah')->where('kode', $validated['kabupaten'])->value('nama');
            $merkText = DB::table('merks')->where('id', $validated['id_merks'])->value('name');
            $ukuranText = DB::table('ukurans')->where('id', $validated['id_ukurans'])->value('name');
            $motifText = DB::table('motifs')->where('id', $validated['id_motifs'])->value('name');

            // Lampiran Bukti Komplain (buat jadi link)
            $lampiranBuktiRecords = DB::table('form_lkp_lampiran_bukti')->where('nomor', $formLkp->nomor)->get();
            $lampiranBuktiText = '';
            foreach ($lampiranBuktiRecords as $row) {
                $url = url('lampiran_bukti/' . $row->lampiran);
                $lampiranBuktiText .= "<a href='$url' target='_blank'>{$row->lampiran}</a>, ";
            }
            $lampiranBuktiText = rtrim($lampiranBuktiText, ', ');

            // Lampiran SJ dan Batch
            $lampiranSJText = $lampiranSJPath ? "<a href='" . url($lampiranSJPath) . "' target='_blank'>" . basename($lampiranSJPath) . "</a>" : '';
            $lampiranBatchText = $lampiranBatchPath ? "<a href='" . url($lampiranBatchPath) . "' target='_blank'>" . basename($lampiranBatchPath) . "</a>" : '';

            // Kirim Email via PHPMailer
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'mail.kobintiles.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'no-reply@kobintiles.com';
                $mail->Password   = 'ahgnA2@t,[7N'; // Ganti jika perlu
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                // Pengirim & Penerima
                $mail->setFrom('no-reply@kobintiles.com', 'Laporan Komplain Pelanggan');
                $mail->addAddress('it.kobin@gmail.com', 'IT Kobin');
                // $mail->addAddress('admsales.kobin@gmail.com', 'Admin Sales Kobin');
                if (!empty($validated['email'])) {
                    $mail->addAddress($validated['email'], 'Laporan Komplain Pelanggan');
                }

                // Konten email
                $mail->isHTML(true);
                $mail->Subject = "Laporan Komplain Pelanggan";
                $mail->Body = "
                    Nomor : {$validated['nomor']}<br/>
                    Tanggal : {$validated['tanggal']}<br/>
                    Nama / Company : {$validated['nama']}<br/>
                    Lokasi Pasang : {$provinsiText}, {$kabupatenText}<br/>
                    Via Agen : {$validated['via_agen']}<br/>
                    No. SJ : {$validated['no_sj']}<br/>
                    Tanggal Pembelian : {$validated['tanggal_pembelian']}<br/>
                    Tanggal Komplain : {$validated['tanggal_komplain']}<br/>
                    Salesman/Salesgirl : {$validated['sales']}<br/>
                    Merek : {$merkText}<br/>
                    Ukuran : {$ukuranText}<br/>
                    Motif : {$motifText}<br/>
                    KW : {$validated['kw']}<br/>
                    Tonality : {$validated['tonality']}<br/>
                    Kaliber : {$validated['kaliber']}<br/>
                    Batch Produksi : {$validated['batch']}<br/>
                    Jumlah Order : {$validated['jumlah_order']}<br/>
                    Jumlah Kirim : {$validated['jumlah_kirim']}<br/>
                    Jumlah Komplain : {$validated['jumlah_komplain']}<br/>
                    Jenis Komplain : {$validated['jenis_komplain']}<br/>
                    Penyelesaian yang dikehendaki : {$validated['penyelesaian']}<br/>
                    <hr/>
                    <i>Mohon untuk tidak membalas email ini, karena ini adalah email otomatis.</i>
                ";

                $mail->send();
            } catch (Exception $e) {
                \Log::error("Gagal mengirim email komplain: {$mail->ErrorInfo}");
            }



            return redirect()->route('komplain.index')->with('success', 'Komplain berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan komplain: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function showKomplain($id)
    {
        $form = FormLkp::with(['merks', 'ukurans', 'motifs','user'])->findOrFail($id);
        $lampiranBukti = DB::table('form_lkp_lampiran_bukti')
            ->where('nomor', $form->nomor)
            ->pluck('lampiran'); // Ambil hanya nama file


            // Ambil nama provinsi (kode 2 digit)
            $kodeProvinsi = substr($form->provinsi, 0, 2);
            $namaProvinsi = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 2')
                ->where('kode', $kodeProvinsi)
                ->value('nama');

            // Ambil nama kabupaten (kode 5 digit)
            $kodeKabupaten = substr($form->kabupaten, 0, 5);
            $namaKabupaten = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 5')
                ->where('kode', $kodeKabupaten)
                ->value('nama');
        // dd($form);

        return view('komplain.show', compact('form','lampiranBukti', 'namaProvinsi', 'namaKabupaten'));
    }

    public function updateKomplain(Request $request, $id)
    {
        $form = FormLkp::findOrFail($id);
        $user = auth()->user()->name;

        // === QMS ===
        if ($user === 'QMS') {
            $request->validate([
                'analisa' => 'required|string',
                'lampiran_analisa' => 'nullable|file'
            ]);

            $form->analisa = $request->analisa;
            $form->approval1_by = 'QMS';
            $form->approval1_date = now()->format('Y-m-d');
            $form->approval1_time = now()->format('H:i:s');
            $form->approval1_status = 1;
            $form->flag = 1;

            if ($request->hasFile('lampiran_analisa')) {
                $file = $request->file('lampiran_analisa');
                $namaFile = $form->nomor . '_analisa_' . time() . '_' . \Illuminate\Support\Str::random(5) . '.' . $file->getClientOriginalExtension();

                $tujuanFolder = public_path('lampiran_analisa');
                if (!file_exists($tujuanFolder)) {
                    mkdir($tujuanFolder, 0755, true);
                }

                $file->move($tujuanFolder, $namaFile);
                $form->lampiran_analisa = 'lampiran_analisa/' . $namaFile;
            }

            $form->save();
            return redirect()->route('komplain.show', $id)->with('success', 'Analisa berhasil disimpan oleh QMS.');
        }



        // === GM ===
        if ($user === 'GM') {
            if ($request->approval2_status == 1) {
                $form->flag = 2;
                $form->approval2_status = 1;
            } elseif ($request->approval2_status == -1) {
                $form->flag = -1;
                $form->approval2_status = -1;
                $form->status = -1;
            }

            $form->approval2_by = 'GM';
            $form->approval2_date = now()->format('Y-m-d');
            $form->approval2_time = now()->format('H:i:s');
            $form->save();

            return redirect()->route('komplain.show', $id)->with('success', 'Validasi GM berhasil diproses.');
        }

        // === SLS ===
        if ($user === 'SLS') {
            $request->validate([
                'keputusan' => 'required|string',
            ]);

            $form->keputusan = $request->keputusan;
            $form->approval3_by = 'SLS';
            $form->approval3_date = now()->format('Y-m-d');
            $form->approval3_time = now()->format('H:i:s');
            $form->approval3_status = 1;
            $form->flag = 3;

            $form->save();
            return redirect()->route('komplain.show', $id)->with('success', 'Keputusan SLS berhasil disimpan.');
        }

        // === DIR ===
        if ($user === 'DIR' && $form->flag == 3 && $form->approval3_status == 1) {
            if ($request->approval4_status == 1) {
                $form->flag = 4;
                $form->status = 1;
                $form->approval4_status = 1;
            } elseif ($request->approval4_status == -1) {
                $form->flag = -2;
                $form->approval4_status = -1;
                $form->status = -2;
            }

            $form->approval4_by = 'DIR';
            $form->approval4_date = now()->format('Y-m-d');
            $form->approval4_time = now()->format('H:i:s');
            $form->save();

            return redirect()->route('komplain.show', $id)->with('success', 'Approval akhir oleh DIR berhasil diproses.');
        }

        return redirect()->route('komplain.show', $id)->with('warning', 'Tidak ada aksi yang dilakukan.');
    }

    public function exportPdf(Request $request)
    {
        $query = FormLkp::query();

        $allowedNames = ['QMS', 'SLS', 'DIR', 'GM'];
        $currentName = auth()->user()->name;

        if (!in_array($currentName, $allowedNames)) {
            $query->where('created_by', $currentName);
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_date', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_date', '<=', $request->tanggal_akhir);
        }

        $formLkps = $query->orderBy('id', 'desc')->get();

        $pdf = Pdf::loadView('komplain.pdf', compact('formLkps'))->setPaper('A4', 'landscape');
        return $pdf->download('data-komplain.pdf');
    }

    public function exportExcel(Request $request)
    {
        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');

        // return Excel::download(new KomplainExport($tanggalAwal, $tanggalAkhir), 'data_komplain.xlsx');
        return Excel::download(new KomplainExport($tanggalAwal, $tanggalAkhir, auth()->user()), 'komplain.xlsx');

    }

    public function sendWa($id)
    {
        try {
            $formLkp = FormLkp::with(['merks', 'ukurans', 'motifs'])->findOrFail($id);
            $userName = auth()->user()->name;

            // Ambil nama provinsi (kode 2 digit)
            $kodeProvinsi = substr($formLkp->provinsi, 0, 2);
            $namaProvinsi = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 2')
                ->where('kode', $kodeProvinsi)
                ->value('nama');

            // Ambil nama kabupaten (kode 5 digit)
            $kodeKabupaten = substr($formLkp->kabupaten, 0, 5);
            $namaKabupaten = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 5')
                ->where('kode', $kodeKabupaten)
                ->value('nama');

            $lokasiPasangText = "Lokasi Pasang : {$namaProvinsi}, {$namaKabupaten}\n";

            $message = "Halo Admin,\n\n"
                . "Ada komplain pelanggan baru dengan detail sebagai berikut:\n\n"
                . "Nomor : {$formLkp->nomor}\n"
                . "Tanggal : {$formLkp->tanggal}\n"
                . "Nama / Company : {$formLkp->nama}\n"
                . $lokasiPasangText
                . "Via Agen : {$formLkp->via_agen}\n"
                . "No. SJ : {$formLkp->no_sj}\n"
                . "Tanggal Pembelian : {$formLkp->tanggal_pembelian}\n"
                . "Tanggal Komplain : {$formLkp->tanggal_komplain}\n"
                . "Salesman/Salesgirl : {$formLkp->sales}\n"
                . "Merek : {$formLkp->merks->name}\n"
                . "Ukuran : {$formLkp->ukurans->name}\n"
                . "Motif : {$formLkp->motifs->name}\n"
                . "KW : {$formLkp->kw}\n"
                . "Tonality : {$formLkp->tonality}\n"
                . "Kaliber : {$formLkp->kaliber}\n"
                . "Batch Produksi : {$formLkp->batch}\n"
                . "Jumlah Order : {$formLkp->jumlah_order}\n"
                . "Jumlah Kirim : {$formLkp->jumlah_kirim}\n"
                . "Jumlah Komplain : {$formLkp->jumlah_komplain}\n"
                . "Jenis Komplain : {$formLkp->jenis_komplain}\n"
                . "Penyelesaian yang dikehendaki : {$formLkp->penyelesaian}\n\n"
                . "Mohon untuk tidak membalas pesan ini, karena ini adalah pesan otomatis.";

            $phone = "6287715755505";
            $waUrl = "https://api.whatsapp.com/send?phone=$phone&text=" . urlencode($message);

            return response()->json(['url' => $waUrl]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




  



}
