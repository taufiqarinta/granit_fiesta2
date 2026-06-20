<?php

namespace App\Http\Controllers;

use App\Models\MasterTarget;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya tampilkan data yang statusnya active
        $masterTargets = MasterTarget::where('status', 'active')
                            ->latest()
                            ->paginate(10);
        
        return view('mastertarget.index', compact('masterTargets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mastertarget.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|string|max:255|unique:master_targets,target',
            'point' => 'required|integer|min:0',
            'kupon' => 'required|integer|min:0',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after:periode_awal'
        ]);

        // Default status active
        $validated['status'] = 'active';

        MasterTarget::create($validated);

        return redirect()->route('mastertarget.index')
            ->with('success', 'Master Target berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $masterTarget = MasterTarget::findOrFail($id);
        // Pastikan hanya bisa akses data active
        if ($masterTarget->status === 'inactive') {
            abort(404);
        }
        
        return view('mastertarget.show', compact('masterTarget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterTarget = MasterTarget::findOrFail($id);
        // Pastikan hanya bisa edit data active
        if ($masterTarget->status === 'inactive') {
            abort(404);
        }
        
        return view('mastertarget.edit', compact('masterTarget'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterTarget = MasterTarget::findOrFail($id);
        // Pastikan hanya bisa update data active
        if ($masterTarget->status === 'inactive') {
            abort(404);
        }

        $validated = $request->validate([
            'target' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_targets')->ignore($masterTarget->id)
            ],
            'point' => 'required|integer|min:0',
            'kupon' => 'required|integer|min:0',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after:periode_awal'
        ]);

        $masterTarget->update($validated);

        return redirect()->route('mastertarget.index')
            ->with('success', 'Master Target berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $masterTarget = MasterTarget::findOrFail($id);
        $masterTarget->update(['status' => 'inactive']);

        return redirect()->route('mastertarget.index')
            ->with('success', 'Master Target berhasil dinonaktifkan.');
    }

    /**
     * Method untuk mengaktifkan kembali data yang inactive
     */
    public function restore($id)
    {
        $masterTarget = MasterTarget::findOrFail($id);
        $masterTarget->update(['status' => 'active']);

        return redirect()->route('mastertarget.index')
            ->with('success', 'Master Target berhasil diaktifkan kembali.');
    }

    /**
     * Method untuk menampilkan data yang inactive
     */
    public function trash()
    {
        $masterTargets = MasterTarget::where('status', 'inactive')
                            ->latest()
                            ->paginate(10);
        
        return view('mastertarget.trash', compact('masterTargets'));
    }
}