<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Di balik reverse proxy hosting (HTTPS diakhiri di proxy): isi TRUSTED_PROXIES=*
        // agar Laravel membaca X-Forwarded-Proto/Host dan membuat URL https yang benar.
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Format error API yang konsisten: { success: false, message, errors? }
        $isApi = fn (Request $request) => $request->is('api/*');

        $envelope = function (string $message, int $status, array $errors = []) {
            $body = ['success' => false, 'message' => $message];

            if ($errors) {
                $body['errors'] = $errors;
            }

            return response()->json($body, $status);
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi, $envelope) {
            if ($isApi($request)) {
                return $envelope('Data yang dikirim tidak valid.', 422, $e->errors());
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi, $envelope) {
            if ($isApi($request)) {
                return $envelope('Tidak terautentikasi. Sertakan token Bearer yang valid.', 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($isApi, $envelope) {
            if ($isApi($request)) {
                return $envelope($e->getMessage() ?: 'Anda tidak memiliki hak akses.', 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isApi, $envelope) {
            if ($isApi($request)) {
                return $envelope('Data tidak ditemukan.', 404);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApi, $envelope) {
            if (! $isApi($request)) {
                return null;
            }

            $status = $e->getStatusCode();

            $message = match ($status) {
                404     => 'Endpoint atau data tidak ditemukan.',
                405     => 'Metode HTTP tidak diizinkan untuk endpoint ini.',
                429     => 'Terlalu banyak permintaan. Coba lagi beberapa saat.',
                default => $e->getMessage() ?: 'Terjadi kesalahan.',
            };

            return $envelope($message, $status);
        });

        $exceptions->render(function (\Throwable $e, Request $request) use ($isApi, $envelope) {
            // Saat APP_DEBUG=true biarkan Laravel menampilkan detail error.
            if ($isApi($request) && ! config('app.debug')) {
                return $envelope('Terjadi kesalahan pada server.', 500);
            }
        });
    })->create();
