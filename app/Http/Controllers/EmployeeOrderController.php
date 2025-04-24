<?php

namespace App\Http\Controllers;

use App\Events\FileLocked;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeOrderController extends Controller
{
    public function index()
    {
        $pageTitle = 'Orders';
        $orders = Order::with(['files', 'admin'])
            ->where('status', 'in_progress')
            ->paginate(20);

        return view('employee.orders.index', compact('pageTitle', 'orders'));
    }

    public function show($id)
    {
        $order = Order::with(['files', 'admin'])->findOrFail($id);
        return view('employee.orders.show', [
            'pageTitle' => 'Order Show',
            'order' => $order,
            'claimedFiles' => $order->files()
                ->where('claimed_by', auth()->id())
                ->where('status', 'claimed')
                ->get()
        ]);
    }

    public function lockFile(Request $request, $id)
    {
        $request->validate([
            'file_id' => 'required|exists:order_files,id'
        ]);

        $order = Order::with(['files', 'admin'])->findOrFail($id);

        $file = $order->files()->where('id', $request->file_id)->firstOrFail();

        if ($file->status !== 'unclaimed' || ($file->locked_by && $file->locked_by !== auth()->id())) {
            return response()->json(['locked' => true], 423);
        }

        $file->update([
            'locked_by' => auth()->id(),
            'locked_at' => now(),
        ]);

        broadcast(new FileLocked($file))->toOthers();

        return response()->json(['locked' => false]);
    }

    public function claimBatch(Request $request, $id)
    {
        $request->validate([
            'file_id' => 'required|array',
            'file_id.*' => 'exists:order_files,id',
        ]);

        $order = Order::with(['files', 'admin'])->findOrFail($id);

        $files = $order->files()
            ->whereIn('id', $request->file_id)
            ->where('status', 'unclaimed')
            ->get();

        DB::transaction(function () use ($files) {
            $files->each->update([
                'status' => 'claimed',
                'claimed_by' => auth()->id(),
                'claimed_at' => now(),
                'locked_by' => null,
                'locked_at' => null,
            ]);
        });

        return backWithError('File has been claimed');
    }


}
