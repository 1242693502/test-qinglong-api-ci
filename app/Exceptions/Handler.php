<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     *
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if ($this->isRequestUsingApiResponse()) {
            $this->apiHandler()->report($exception);
        } else {
            parent::report($exception);
        }
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     *
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($this->isRequestUsingApiResponse($request)) {
            return $this->apiHandler()->render($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * 判断当前请求是否归属于Api请求
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isRequestUsingApiResponse($request = null)
    {
        if (!$request) {
            if (!$this->container || !$this->container->has('request')) {
                return false;
            }
            $request = $this->container->make('request');
        }

        if ($request::hasMacro('usingApiResponse')) {
            return $request->usingApiResponse();
        }

        return false;
    }

    /**
     * api异常处理
     *
     * @return \Urland\Api\Exceptions\ApiHandler
     */
    protected function apiHandler()
    {
        return $this->container->make(\Urland\Api\Exceptions\ApiHandler::class);
    }
}
