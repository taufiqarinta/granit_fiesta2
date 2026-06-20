<?php

namespace App\Http\Controllers;

use App\Models\OrderGathering;
use App\Models\OrderGatheringDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderGatheringController extends Controller
{
    public function index()
    {
        $orders = OrderGathering::with('details')->latest()->paginate(10);
        return view('order-gathering.index', compact('orders'));
    }

    public function create()
    {
        return view('order-gathering.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_agen' => 'required|string',
            'nama_toko' => 'required|string',
            'provinsi' => 'required|string',
            'kota_kab' => 'required|string',
            'alamat' => 'required|string',
            'target' => 'required|string',
            'tanggal_order' => 'required|date',
            'brands' => 'required|array|min:1',
            'brands.*.brand' => 'required|string',
            'brands.*.motif' => 'required|string',
            'brands.*.jumlah_box' => 'required|integer|min:0',
            'brands.*.jumlah_point' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total point
            $totalPoint = collect($request->brands)->sum('jumlah_point');

            // Simpan order gathering
            $order = OrderGathering::create([
                'nama_agen' => $request->nama_agen,
                'nama_toko' => $request->nama_toko,
                'provinsi' => $request->provinsi,
                'kota_kab' => $request->kota_kab,
                'alamat' => $request->alamat,
                'target' => $request->target,
                'total_point' => $totalPoint,
                'tanggal_order' => $request->tanggal_order,
            ]);

            // Simpan detail order
            foreach ($request->brands as $brand) {
                OrderGatheringDetail::create([
                    'order_gathering_id' => $order->id,
                    'brand' => $brand['brand'],
                    'motif' => $brand['motif'],
                    'jumlah_box' => $brand['jumlah_box'],
                    'jumlah_point' => $brand['jumlah_point'],
                ]);
            }

            DB::commit();
            return redirect()->route('order-gathering.index')
                ->with('success', 'Order gathering berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(OrderGathering $orderGathering)
    {
        $orderGathering->load('details');
        return view('order-gathering.show', compact('orderGathering'));
    }

    public function edit(OrderGathering $orderGathering)
    {
        $orderGathering->load('details');
        return view('order-gathering.edit', compact('orderGathering'));
    }

    public function update(Request $request, OrderGathering $orderGathering)
    {
        $request->validate([
            'nama_agen' => 'required|string',
            'nama_toko' => 'required|string',
            'provinsi' => 'required|string',
            'kota_kab' => 'required|string',
            'alamat' => 'required|string',
            'target' => 'required|string',
            'tanggal_order' => 'required|date',
            'brands' => 'required|array|min:1',
            'brands.*.brand' => 'required|string',
            'brands.*.motif' => 'required|string',
            'brands.*.jumlah_box' => 'required|integer|min:0',
            'brands.*.jumlah_point' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total point
            $totalPoint = collect($request->brands)->sum('jumlah_point');

            // Update order gathering
            $orderGathering->update([
                'nama_agen' => $request->nama_agen,
                'nama_toko' => $request->nama_toko,
                'provinsi' => $request->provinsi,
                'kota_kab' => $request->kota_kab,
                'alamat' => $request->alamat,
                'target' => $request->target,
                'total_point' => $totalPoint,
                'tanggal_order' => $request->tanggal_order,
            ]);

            // Hapus detail lama
            $orderGathering->details()->delete();

            // Simpan detail baru
            foreach ($request->brands as $brand) {
                OrderGatheringDetail::create([
                    'order_gathering_id' => $orderGathering->id,
                    'brand' => $brand['brand'],
                    'motif' => $brand['motif'],
                    'jumlah_box' => $brand['jumlah_box'],
                    'jumlah_point' => $brand['jumlah_point'],
                ]);
            }

            DB::commit();
            return redirect()->route('order-gathering.index')
                ->with('success', 'Order gathering berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(OrderGathering $orderGathering)
    {
        try {
            $orderGathering->delete();
            return redirect()->route('order-gathering.index')
                ->with('success', 'Order gathering berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}