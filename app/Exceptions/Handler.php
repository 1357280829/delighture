<?php

namespace App\Exceptions;

use App\Enums\Code;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * 重写：验证异常时抛出JSON响应
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => Code::getDescription(Code::FailedValidate),
            'custom_code' => Code::FailedValidate,
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * 重写：通用异常时抛出JSON响应格式
     *
     * @param Throwable $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'custom_code' => $e->getCode(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
            'custom_code' => $e->getCode(),
        ];
    }
}
