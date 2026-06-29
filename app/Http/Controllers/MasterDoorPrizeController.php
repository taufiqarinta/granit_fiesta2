<?php

namespace App\Http\Controllers;

use App\Models\Doorprize;
use Illuminate\Http\Request;
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
                            ->latest()
                            ->paginate(10);
        
        return view('masterdoorprize.index', compact('masterDoorprizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('masterdoorprize.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_doorprize' => 'required|string',
            'jumlah_doorprize' => 'required|integer|min:0',
            'nama_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // max 2MB
        ]);

        // Upload file
        if ($request->hasFile('nama_file')) {
            $file = $request->file('nama_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/doorprizes'), $filename);
            $validated['nama_file'] = $filename;
        }

        // Default status active
        $validated['status'] = 1;

        Doorprize::create($validated);

        return redirect()->route('masterdoorprize.index')
            ->with('success', 'Master Doorprize berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $masterDoorprize = Doorprize::findOrFail($id);
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
        $masterDoorprize = Doorprize::findOrFail($id);
        // Pastikan hanya bisa edit data active
        if ($masterDoorprize->status != 1) {
            abort(404);
        }
        
        return view('masterdoorprize.edit', compact('masterDoorprize'));
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

        $validated = $request->validate([
            'nama_doorprize' => 'required|string',
            'jumlah_doorprize' => 'required|integer|min:0',
            'nama_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // max 2MB
        ]);

        // Upload file baru jika ada
        if ($request->hasFile('nama_file')) {
            // Hapus file lama jika ada
            if ($masterDoorprize->nama_file && file_exists(public_path('images/doorprizes/' . $masterDoorprize->nama_file))) {
                unlink(public_path('images/doorprizes/' . $masterDoorprize->nama_file));
            }
            
            $file = $request->file('nama_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/doorprizes'), $filename);
            $validated['nama_file'] = $filename;
        }

        $masterDoorprize->update($validated);

        return redirect()->route('masterdoorprize.index')
            ->with('success', 'Master Doorprize berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $masterDoorprize = Doorprize::findOrFail($id);
        $masterDoorprize->update(['status' => 0]);

        return redirect()->route('masterdoorprize.index')
            ->with('success', 'Master Doorprize berhasil dinonaktifkan.');
    }

    /**
     * Method untuk mengaktifkan kembali data yang inactive
     */
    public function restore($id)
    {
        $masterDoorprize = Doorprize::findOrFail($id);
        $masterDoorprize->update(['status' => 1]);

        return redirect()->route('masterdoorprize.index')
            ->with('success', 'Master Doorprize berhasil diaktifkan kembali.');
    }

    /**
     * Method untuk menampilkan data yang inactive
     */
    public function trash()
    {
        $masterDoorprizes = Doorprize::where('status', 0)
                            ->latest()
                            ->paginate(10);
        
        return view('masterdoorprize.trash', compact('masterDoorprizes'));
    }
}