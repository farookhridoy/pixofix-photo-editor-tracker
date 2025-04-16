<?php

use Illuminate\Support\Facades\DB;

/**
 * @param $option_name
 * @return bool
 */
function isOptionPermitted($option_name)
{
    return auth()->user()->can($option_name);
}

/**
 * @param $message
 * @return \Illuminate\Http\RedirectResponse
 */
function backWithError($message)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'error'
    ];
    return back()->with($notification);
}


/**
 * @param $message
 * @return \Illuminate\Http\RedirectResponse
 */
function backWithSuccess($message)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'success'
    ];
    return back()->with($notification);
}

/**
 * @param $message
 * @return \Illuminate\Http\RedirectResponse
 */
function backWithWarning($message)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'warning'
    ];
    return back()->with($notification);
}

/**
 * @param $message
 * @param $route
 * @return \Illuminate\Http\RedirectResponse
 */
function redirectBackWithWarning($message, $route)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'warning'
    ];
    return redirect()->route($route)->with($notification);
}

/**
 * @param $message
 * @param $route
 * @return \Illuminate\Http\RedirectResponse
 */
function redirectBackWithError($message, $route)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'error'
    ];
    return redirect()->route($route)->with($notification);
}

/**
 * @param $message
 * @param $route
 * @return \Illuminate\Http\RedirectResponse
 */
function redirectBackWithSuccess($message, $route)
{
    $notification = [
        'message' => $message,
        'alert-type' => 'success'
    ];
    return redirect()->route($route)->with($notification);
}

/**
 * @param $time
 * @return string
 */
function humanReadableTime($time)
{
    $explode = explode(':', $time);
    return (isset($explode[0]) && $explode[0] > 0 ? ' ' . (int)($explode[0]) . 'h' : '') . (isset($explode[1]) && $explode[1] > 0 ? ' ' . (int)($explode[1]) . 'm' : '') . (isset($explode[2]) && $explode[2] > 0 ? ' ' . (int)($explode[2]) . 's' : '');
}


function getProgressAttribute($order)
{
    $fileCount = $order->files->count();
    $completeFiles = $order->files->where('status', 'completed')->count();

    return round(($completeFiles / $fileCount) * 100, 2);
}

/**
 * @param $length
 * @param $prefix
 * @param $table
 * @param $field
 * @return string
 */
function uniqueCode($length, $prefix, $table, $field)
{
    $prefix_length = strlen($prefix);
    $max_id = DB::table($table)->count($field);
    $new = (int)($max_id);
    $new++;
    $number_of_zero = $length - $prefix_length - strlen($new);
    $zero = str_repeat("0", $number_of_zero);
    $made_id = $prefix . $zero . $new;
    return $made_id;
}

function fileUpload($filedata, $folderName)
{

    $fileType = $filedata->getClientOriginalExtension();
    $fileName = rand(1, 1000) . date('dmyhis') . "." . $fileType;
    $path2 = $folderName;
    if (!file_exists(public_path($path2))) {
        mkdir(public_path($path2), 0777, true);
    }
    $img = $filedata->move(public_path($path2), $fileName);
    return $photoUploadedPath = $path2 . '/' . $fileName;
}
