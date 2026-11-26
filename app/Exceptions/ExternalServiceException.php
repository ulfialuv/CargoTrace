<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Layanan eksternal (geocoding / weather) gagal: timeout, error HTTP, atau
 * respons tidak bisa dipakai. Controller mengubahnya menjadi JSON 502/504
 * tanpa membocorkan detail teknis ke pengguna.
 */
class ExternalServiceException extends RuntimeException
{
    public function __construct(string $message, private int $status = 502, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }
}
