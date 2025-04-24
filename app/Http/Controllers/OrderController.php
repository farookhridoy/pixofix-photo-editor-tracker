<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderFolder;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $prefix = 'ORD-' . date('y', strtotime(date('Y-m-d'))) . '-';
        $sku = uniqueCode(14, $prefix, 'orders', 'id');
        $categoryOptions = Category::treeList();
        return view('orders.create', compact('sku', 'pageTitle', 'categoryOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'folder' => 'required|array',
            'folder.*' => 'file|mimes:jpg,jpeg,png,gif,webp,bmp,svg'
        ]);

        DB::beginTransaction();

        try {

            $order = Order::create($request->all());

            $tempPath = 'temp/' . $order->order_number;

            foreach ($request->file('folder') as $uploadedFile) {
                $relativePath = $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs($tempPath, $relativePath, 'temp');
            }

            ProcessOrderFolder::dispatch($order, $tempPath);

            DB::commit();
            return redirect()->route('orders.show', $order);
        } catch (\Exception $ex) {
            DB::rollBack();
            return backWithError($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $data = [
            'order' => $order->load('files', 'category'),
            'activities' => $order->activities()->latest()->get(),
            'pageTitle' => 'Order View',
            'progress' => getProgressAttribute($order)
        ];

        return view('orders.show', $data);
    }

    public function approve(Order $order)
    {
        $order->update(['status' => 'completed']);
        return redirect()->route('orders.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $pageTitle = 'Edit Order';
        $categoryOptions = Category::treeList();
        unset($categoryOptions[$order->category_id]);

        return view('orders.edit', compact('pageTitle', 'categoryOptions', 'order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'folder' => 'nullable|array',
            'folder.*' => 'file|mimes:jpg,jpeg,png,gif,webp,bmp,svg'
        ]);

        DB::beginTransaction();

        try {
            // Update the order details
            $order->update([
                'title' => $request->input('title'),
                'category_id' => $request->input('category_id'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
            ]);

            // If new files are uploaded
            if ($request->hasFile('folder')) {
                $tempPath = 'temp/' . $order->order_number;

                foreach ($request->file('folder') as $uploadedFile) {
                    $relativePath = $uploadedFile->getClientOriginalName();
                    $uploadedFile->storeAs($tempPath, $relativePath, 'temp');
                }

                // Dispatch job to re-process folder
                ProcessOrderFolder::dispatch($order, $tempPath);
            }

            DB::commit();

            return redirectBackWithSuccess('Order updated successfully!', 'orders.index');
        } catch (\Exception $ex) {
            DB::rollBack();
            return backWithError('Something went wrong: ' . $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::whereHas('files', function ($query) use ($id) {
            $query->where('status', 'unclaimed');
        })->findOrFail($id);

        DB::beginTransaction();

        try {
            // Delete associated files from public storage
            foreach ($order->files as $file) {
                if (Storage::disk('public')->exists($file->filepath)) {
                    Storage::disk('public')->delete($file->filepath);
                }
            }

            // Delete the folder structure
            $folderPath = "{$order->order_number}/original";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory("{$order->order_number}");
            }

            // Delete related database records
            $order->files()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order and associated files deleted.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function fileDelete(string $id)
    {
        $file = OrderFile::where('id', $id)->where('status', 'unclaimed')->first();
        try {
            $file->delete();
            return back()->with('success', 'Order File deleted.');
        } catch (\Exception $ex) {
            return backWithError($ex->getMessage());
        }
    }
}
