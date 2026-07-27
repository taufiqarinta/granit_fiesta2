<?php

namespace App\Http\Controllers;

use App\Models\Doorprize;
use App\Models\DoorprizeLokasi;
use App\Models\MasterLokasiEvent;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterDoorPrizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya tampilkan data yang statusnya active
        $masterDoorprizes = Doorprize::where('status', 1)
                            ->with('lokasi') // Eager loading relasi lokasi
                            ->latest()
                            ->paginate(10);
        
        return view('masterdoorprize.index', compact('masterDoorprizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasiEvents = MasterLokasiEvent::active()->orderBy('nama_lokasi')->get();
        return view('masterdoorprize.create', compact('lokasiEvents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_doorprize' => 'required|string|max:255',
            'nama_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'required|array|min:1',
            'lokasi.*.lokasi_event' => 'required|string|distinct',
            'lokasi.*.jumlah_doorprize' => 'required|integer|min:0'
        ], [
            'lokasi.required' => 'Minimal 1 lokasi event harus dipilih',
            'lokasi.*.lokasi_event.distinct' => 'Lokasi event tidak boleh duplikat',
            'lokasi.*.jumlah_doorprize.min' => 'Jumlah doorprize minimal 0'
        ]);

        try {
            DB::beginTransaction();

            // Upload file
            $fileName = null;
            if ($request->hasFile('nama_file')) {
                $file = $request->file('nama_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/doorprizes'), $fileName);
            }

            // Create Doorprize
            $doorprize = Doorprize::create([
                'nama_doorprize' => $request->nama_doorprize,
                'nama_file' => $fileName,
                'status' => 1
            ]);

            // Create Lokasi
            foreach ($request->lokasi as $lokasi) {
                // Cek apakah sudah ada kombinasi doorprize_id + lokasi_event
                $existing = DoorprizeLokasi::where('doorprize_id', $doorprize->id)
                    ->where('lokasi_event', strtoupper($lokasi['lokasi_event']))
                    ->first();

                if ($existing) {
                    // Update jika sudah ada
                    $existing->update([
                        'jumlah_doorprize' => $lokasi['jumlah_doorprize'],
                        'status' => 1
                    ]);
                } else {
                    // Create baru
                    DoorprizeLokasi::create([
                        'doorprize_id' => $doorprize->id,
                        'lokasi_event' => strtoupper($lokasi['lokasi_event']),
                        'jumlah_doorprize' => $lokasi['jumlah_doorprize'],
                        'status' => 1
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('masterdoorprize.index')
                ->with('success', 'Master Doorprize berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan doorprize: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $masterDoorprize = Doorprize::with('lokasi')->findOrFail($id);
        
        // Pastikan hanya bisa akses data active
        if ($masterDoorprize->status != 1) {
            abort(404);
        }
        
        return view('masterdoorprize.show', compact('masterDoorprize'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterDoorprize = Doorprize::with('lokasi')->findOrFail($id);
        
        // Pastikan hanya bisa edit data active
        if ($masterDoorprize->status == 0) {
            abort(404);
        }
        
        $lokasiEvents = MasterLokasiEvent::active()->orderBy('nama_lokasi')->get();
        
        return view('masterdoorprize.edit', compact('masterDoorprize', 'lokasiEvents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterDoorprize = Doorprize::findOrFail($id);
        
        // Pastikan hanya bisa update data active
        if ($masterDoorprize->status == 0) {
            abort(404);
        }

        $request->validate([
            'nama_doorprize' => 'required|string|max:255',
            'nama_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'required|array|min:1',
            'lokasi.*.lokasi_event' => 'required|string|distinct',
            'lokasi.*.jumlah_doorprize' => 'required|integer|min:0'
        ], [
            'lokasi.required' => 'Minimal 1 lokasi event harus dipilih',
            'lokasi.*.lokasi_event.distinct' => 'Lokasi event tidak boleh duplikat',
            'lokasi.*.jumlah_doorprize.min' => 'Jumlah doorprize minimal 0'
        ]);

        try {
            DB::beginTransaction();

            // Update data doorprize
            $dataToUpdate = [
                'nama_doorprize' => $request->nama_doorprize
            ];

            // Upload file baru jika ada
            if ($request->hasFile('nama_file')) {
                // Hapus file lama jika ada
                if ($masterDoorprize->nama_file && file_exists(public_path('images/doorprizes/' . $masterDoorprize->nama_file))) {
                    unlink(public_path('images/doorprizes/' . $masterDoorprize->nama_file));
                }
                
                $file = $request->file('nama_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/doorprizes'), $fileName);
                $dataToUpdate['nama_file'] = $fileName;
            }

            $masterDoorprize->update($dataToUpdate);

            // Update lokasi
            // Dapatkan list lokasi yang ada
            $existingLokasi = DoorprizeLokasi::where('doorprize_id', $id)->get();
            $existingLokasiEvents = $existingLokasi->pluck('lokasi_event')->toArray();
            
            $newLokasiEvents = array_map(function($lokasi) {
                return strtoupper($lokasi['lokasi_event']);
            }, $request->lokasi);

            // Hapus lokasi yang tidak ada di request
            foreach ($existingLokasi as $lokasi) {
                if (!in_array($lokasi->lokasi_event, $newLokasiEvents)) {
                    $lokasi->delete();
                }
            }

            // Update atau create lokasi
            foreach ($request->lokasi as $lokasi) {
                $lokasiEvent = strtoupper($lokasi['lokasi_event']);
                $jumlah = $lokasi['jumlah_doorprize'];
                
                DoorprizeLokasi::updateOrCreate(
                    [
                        'doorprize_id' => $id,
                        'lokasi_event' => $lokasiEvent
                    ],
                    [
                        'jumlah_doorprize' => $jumlah,
                        'status' => 1
                    ]
                );
            }

            DB::commit();

            return redirect()->route('masterdoorprize.index')
                ->with('success', 'Master Doorprize berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui doorprize: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $masterDoorprize = Doorprize::findOrFail($id);
            $masterDoorprize->update(['status' => 0]);
            
            // Nonaktifkan semua lokasi terkait
            DoorprizeLokasi::where('doorprize_id', $id)->update(['status' => 0]);
            
            DB::commit();

            return redirect()->route('masterdoorprize.index')
                ->with('success', 'Master Doorprize berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menonaktifkan doorprize: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk mengaktifkan kembali data yang inactive
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $masterDoorprize = Doorprize::findOrFail($id);
            $masterDoorprize->update(['status' => 1]);
            
            // Aktifkan semua lokasi terkait
            DoorprizeLokasi::where('doorprize_id', $id)->update(['status' => 1]);
            
            DB::commit();

            return redirect()->route('masterdoorprize.index')
                ->with('success', 'Master Doorprize berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengaktifkan doorprize: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menampilkan data yang inactive
     */
    public function trash()
    {
        $masterDoorprizes = Doorprize::where('status', 0)
                            ->with('lokasi')
                            ->latest()
                            ->paginate(10);
        
        return view('masterdoorprize.trash', compact('masterDoorprizes'));
    }


    public function resetPemenang($lokasi)
    {
        try {
            DB::beginTransaction();

            $updated = Voucher::where('lokasi_event', strtoupper($lokasi))
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'hadiah' => null,
                    'sudah_ditukarkan' => 0,
                    'ditukarkan_at' => null,
                ]);

            DB::commit();

            return redirect()->route('masterdoorprize.index')
                ->with('success', "Berhasil mereset " . $updated . " pemenang doorprize untuk lokasi " . strtoupper($lokasi) . ".");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mereset pemenang: ' . $e->getMessage());
        }
    }
}