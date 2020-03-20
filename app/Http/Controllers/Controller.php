<?php

namespace App\Http\Controllers;

use App\Enums\Code;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function res($code = Code::Success, $data = [], $message = '')
    {
        return response()->json([
            'message' => $message ?: Code::getDescription($code),
            'custom_code' => $code,
            'data' => $data,
        ]);
    }
}
