<?php

namespace App\Http\Controllers;

use App\Events\FileLocked;
use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Mockery\Exception;

class EmployeeOrderController extends Controller
{
    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index()
    {
        $pageTitle = 'Orders';
        $orders = Order::with(['files', 'admin'])
            ->where('status', 'in_progress')
            ->paginate(20);

        return view('employee.orders.index', compact('pageTitle', 'orders'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function show($id)
    {
        $order = Order::with(['files', 'admin'])->findOrFail($id);

        return view('employee.orders.show', [
            'pageTitle' => 'Order Show',
            'order' => $order
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
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

        //broadcast file locked
        broadcast(new FileLocked($file))->toOthers();

        //generate log
        fileLogGenerate($file->id, 'locked', 'Temporary lock due to activity');


        return response()->json(['locked' => false]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function claimBatch(Request $request, $id)
    {
        $request->validate([
            'file_id' => 'required|array',
            'file_id.*' => 'exists:order_files,id',
        ]);

//        DB::beginTransaction();
//        try {
            $order = Order::with(['files', 'admin'])->findOrFail($id);

            $files = $order->files()
                ->whereIn('id', $request->file_id)
                ->where('status', 'unclaimed')
                ->get();

            $files->each->update([
                'status' => 'claimed',
                'claimed_by' => auth()->id(),
                'claimed_at' => now(),
                'locked_by' => null,
                'locked_at' => null,
            ]);

            //generate log
            fileLogGenerate($files->pluck('id')->toArray(), 'claimed', 'File claimed for editing');

            DB::commit();
            //forget the admin dashboard cache
            Cache::forget('admin_dashboard_data');


            return backWithSuccess('File has been claimed successfully');
//        } catch (\Exception $ex) {
//            DB::rollBack();
//            return backWithError($ex->getMessage());
//        }
    }

    public function myBatchIndex($id)
    {
        $order = Order::with(['files', 'admin'])->findOrFail($id);
        return view('employee.orders.myBatchIndex', compact('order'), [
            'pageTitle' => 'My Order Show',
            'order' => $order,
            'claimedFiles' => $order->files()
                ->where('claimed_by', auth()->id())
                ->whereNotIn('status', ['unclaimed'])
                ->paginate(20)
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $file = OrderFile::findOrFail($id);
        return response()->json($file);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $file = OrderFile::find($id);
            $file->update(['status' => $request->status]);

            //generate log
            fileLogGenerate($file->id, $request->status, 'Update File Status as ' . $request->status);
            //forget the admin dashboard cache
            Cache::forget('admin_dashboard_data');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Status has been updated']);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

}
