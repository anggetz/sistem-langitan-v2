<?php

use Illuminate\Http\Response;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ResolvePerguruanTinggiFromDomain;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);


        $middleware->append([
            ResolvePerguruanTinggiFromDomain::class,
        ]);

        // stateful
        $middleware->statefulApi();

        // alias
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthenticateWithToken::class,
            'role' => \App\Http\Middleware\RoleAuthorization::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $e, $request) {


            if ($request->is('api/*')) {

                if ($e instanceof ValidationException) {

                    $responseValidationError = [];
                    $message = "";
                    foreach ($e->errors() as $key => $values) {
                        $responseValidationError[] = ["field" => $key, "message" => $values];
                        $message .= implode(", ", $values) . ", ";
                    }
                    return response()->json([
                        'message' => $message,
                        "status" => false,
                        "data" => null,
                        'errors' => $responseValidationError,
                    ], $e->status);
                }

                if ($e instanceof NotFoundHttpException) {
                    return Response::error(
                        "Halaman tidak ditemukan",
                        Response::HTTP_NOT_FOUND
                    );
                }

                if ($e instanceof QueryException) {
                    return Response::error(
                        "Error query ",
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                if ($e instanceof AccessDeniedHttpException) {
                    return Response::error(
                        "Anda tidak mempunyai akses untuk melakukan aksi ini",
                        Response::HTTP_FORBIDDEN
                    );
                }
            }
        });

        // $exceptions->render(function (NotFoundHttpException $exception, $request) {

        //     return "ok";
        //     if ($request->is('api/*')) {

        //         return Response::error(
        //             "Data tidak ditemukan",
        //             Response::HTTP_NOT_FOUND
        //         );
        //     }

        //     return parent::render($request, $exception);
        // });

        // $exceptions->render(function (QueryException $exception, $request) {

        //     if ($request->is('api/*')) {
        //         return Response::error(
        //             "Error: " . $exception->getMessage(),
        //             Response::HTTP_INTERNAL_SERVER_ERROR
        //         );
        //     }
        //     return parent::render($request, $exception);
        // });

        // $exceptions->render(function (AccessDeniedHttpException $exception, $request) {

        //     if ($request->is('api/*')) {
        //         return Response::error(
        //             "Anda tidak mempunyai akses untuk melakukan aksi ini",
        //             Response::HTTP_UNAUTHORIZED
        //         );
        //     }
        //     return parent::render($request, $exception);
        // });
    })
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api'], // Sesuaikan prefix jika Anda menggunakan prefix kustom untuk broadcasting auth
    )
    ->create();
