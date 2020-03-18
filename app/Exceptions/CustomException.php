<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    /**
     * 将异常渲染至 HTTP 响应值中
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage() ?: '客户端异常',
            'custom_code' => $this->getCode(),
        ], 400);
    }
}
