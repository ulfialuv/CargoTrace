<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Pencatat audit log. Hanya mencatat jika ada pengguna yang login (aksi sistem
 * seperti seeder atau artisan tidak dicatat). Kegagalan mencatat tidak boleh
 * menggagalkan proses utama, jadi error hanya dilaporkan ke log aplikasi.
 */
class AuditLogService
{
    /** Pengguna yang sedang login (web maupun token API). */
    public function record(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $old = null,
        ?array $new = null,
        ?string $reference = null,
    ): ?AuditLog {
        return $this->recordFor(Auth::user(), $action, $module, $description, $subject, $old, $new, $reference);
    }

    /** Pengguna ditentukan sendiri (mis. saat login, sebelum guard terisi). */
    public function recordFor(
        ?Authenticatable $actor,
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $old = null,
        ?array $new = null,
        ?string $reference = null,
    ): ?AuditLog {
        if (! $actor) {
            return null;
        }

        try {
            $request = request();

            return AuditLog::create([
                'user_id'          => $actor->getAuthIdentifier(),
                'user_name'        => $actor->name ?? null,
                'user_email'       => $actor->email ?? null,
                'user_role'        => $actor->role ?? null,
                'action'           => $action,
                'module'           => $module,
                'description'      => mb_substr($description, 0, 500),
                'subject_type'     => $subject ? class_basename($subject) : null,
                'subject_id'       => $subject?->getKey(),
                'reference_number' => $reference,
                'old_values'       => $old ?: null,
                'new_values'       => $new ?: null,
                'ip_address'       => $request?->ip(),
                'user_agent'       => $request ? mb_substr((string) $request->userAgent(), 0, 1000) : null,
            ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
