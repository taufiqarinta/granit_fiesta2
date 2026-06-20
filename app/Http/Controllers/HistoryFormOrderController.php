<?php

namespace App\Http\Controllers;

use App\Models\HistoryFormOrder;
use Illuminate\Http\Request;

class HistoryFormOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoryFormOrder::with('formOrder')
            ->orderBy('created_at', 'desc');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_toko', 'like', "%$search%")
                  ->orWhere('kode_agen', 'like', "%$search%")
                  ->orWhere('nama_agen', 'like', "%$search%")
                  ->orWhere('lokasi_event', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $histories = $query->paginate(20)->withQueryString();

        return view('history-form-order.index', compact('histories'));
    }

    public function show($id)
    {
        $history = HistoryFormOrder::with('formOrder')->findOrFail($id);

        return view('history-form-order.show', compact('history'));
    }
}