<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderFolder;
use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Orders';
        $orders = Order::with(['files', 'admin'])->paginate(20);

        return view('orders.index', compact('pageTitle', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Order';
        $prefix = '#ORD-' . date('y', strtotime(date('Y-m-d'))) . '-';
        $sku = uniqueCode(14, $prefix, 'orders', 'id');

        return view('orders.create', compact('sku', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'folder' => 'required'
        ]);

        $order = Order::create($request->only('title', 'description', 'order_number', 'status'));

        $tempPath = 'temp/' . uniqid();
        $request->file('folder')->storeAs($tempPath, '', 'temp');

        ProcessOrderFolder::dispatch($order, $tempPath);

        return redirect()->route('orders.show', $order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return view('orders.show', [
            'order' => $order->load('files'),
            'activities' => $order->activities()->latest()->get()
        ]);
    }

    public function approve(Order $order)
    {
        $order->update(['status' => 'completed']);
        return redirect()->route('orders.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function fileUploads(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
