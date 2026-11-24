<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

/**
 * Pelanggaran aturan bisnis (mis. mengubah shipment DELIVERED, menghapus
 * shipment yang sudah dibayar). Dilempar oleh service, di-render oleh sini.
 */
class BusinessRuleException extends RuntimeException
{
    public function __construct(string $message, private int $status = 422)
    {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function render(Request $request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $this->getMessage()], $this->status);
        }

        return back()->withErrors(['error' => $this->getMessage()])->withInput();
    }
}
