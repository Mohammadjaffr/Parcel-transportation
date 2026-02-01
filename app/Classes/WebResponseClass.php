<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class WebResponseClass
{
    public static function sendResponse($title, $message, $buttonText = 'حسناً', $route = null, $parameters = [])
    {
        if ($route == null) {
            return redirect()->back()
                ->with('success', true)
                ->with('success_title', $title)
                ->with('success_message', $message)
                ->with('success_buttonText', $buttonText);
        }

        return redirect()->route($route, $parameters)
            ->with('success', true)
            ->with('success_title', $title)
            ->with('success_message', $message)
            ->with('success_buttonText', $buttonText);
    }

    public static function sendError($message, $title = 'حدث خطأ!', $buttonText = 'حسناً')
    {
        return redirect()->back()
            ->with('error', true)
            ->with('error_title', $title)
            ->with('error_message', $message)
            ->with('error_buttonText', $buttonText);
    }

    public static function sendValidationError($validator, $title = 'حدث خطأ!', $buttonText = 'حسناً')
    {
        return redirect()->back()
            ->withErrors($validator)
            ->with('error', true)
            ->with('error_title', $title)
            ->with('error_message', $validator->errors()->first())
            ->with('error_buttonText', $buttonText)
            ->withInput();
    }

    public static function sendExceptionError($e, $title = 'حدث خطأ!', $buttonText = 'حسناً')
    {
        Log::error($e->getMessage());
        return redirect()->back()
            ->with('error', true)
            ->with('error_title', $title)
            ->with('error_message', $e->getMessage())
            ->with('error_buttonText', $buttonText)
            ->withInput();
    }
}
