<?php

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

