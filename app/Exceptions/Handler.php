<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse
    {
        $response = parent::render($request, $e);
        if (!$response) {
            $response = new JsonResponse(['message' => $e->getMessage()], 500);
        }

        try {
            $data = $response->getData(true);

            if (!isset($data['message']) || !$data['message']) {
                switch ($response->getStatusCode()) {
                    case 404:
                        $data['message'] = 'Not Found';
                        break;
                    case 405:
                        $data['message'] = 'Method Not Found';
                        break;
                    case 422:
                        $data = [
                            'errors' => $data,
                            'message' => trans_choice('A Validation Errors Occurred | :count Validation Errors Occur', count($data))
                        ];
                        break;
                    default:
                        $data['message'] = 'Unknown Error';
                }
            }
            $response->setContent(json_encode($data));
        } catch (Throwable $e) {
        }

        return $response;
    }
}
