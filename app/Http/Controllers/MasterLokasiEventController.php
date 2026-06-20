<?php

namespace App\Http\Controllers;

use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MasterLokasiEventController extends Controller
{
    public function index(): View
    {
        $lokasiEvents = MasterLokasiEvent::latest()->paginate(10);
        return view('master-lokasi-event.index', compact('lokasiEvents'));
    }

    public function create(): View
    {
        return view('master-lokasi-event.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_lokasi' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        MasterLokasiEvent::create($request->all());

        return redirect()->route('master-lokasi-event.index')
            ->with('success', 'Lokasi event berhasil ditambahkan.');
    }

    public function show(MasterLokasiEvent $masterLokasiEvent): View
    {
        return view('master-lokasi-event.show', compact('masterLokasiEvent'));
    }

    public function edit(MasterLokasiEvent $masterLokasiEvent): View
    {
        return view('master-lokasi-event.edit', compact('masterLokasiEvent'));
    }

    public function update(Request $request, MasterLokasiEvent $masterLokasiEvent): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_lokasi' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $masterLokasiEvent->update($request->all());

        return redirect()->route('master-lokasi-event.index')
            ->with('success', 'Lokasi event berhasil diperbarui.');
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $masterLokasiEvent = MasterLokasiEvent::findOrFail($id);

        $masterLokasiEvent->update(['status' => "Nonaktif"]); 


        return redirect()->route('master-lokasi-event.index')
            ->with('success', 'Lokasi event berhasil dinonaktifkan.');
    }

    // public function destroy(Request $request, $id)
    // {
    //     $daftartoko = DaftarToko::findOrFail($id);

    //     $daftartoko->update(['status' => 0]); 

    //     LogAktivitas::create([
    //         'user_id'    => auth()->user()->id,
    //         'username'   => auth()->user()->name,
    //         'aksi'       => 'Hapus',
    //         'fitur'      => 'Daftar Toko',
    //         'deskripsi'  => "Menonaktifkan data toko {$daftartoko->kode_toko} - {$daftartoko->nama_toko}",
    //         'ip_address' => $request->ip(),
    //         'device' => Browser::browserName() . ' on ' . Browser::platformName(),
    //         'created_at' => now(),
    //     ]);

    //     return redirect()->route('daftartoko.index')
    //         ->with('success', 'Data toko berhasil dihapus');
    // }
}