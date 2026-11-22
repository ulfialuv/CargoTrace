<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Query dengan filter yang dipakai bersama oleh daftar dan export.
     */
    private function filtered(Request $request): Builder
    {
        $query = AuditLog::query();

        // Pencarian pengguna, email, deskripsi, dan nomor referensi
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    /**
     * Halaman utama Audit Log: daftar dan detail ada di satu view.
     */
    public function index(Request $request)
    {
        $logs = $this->filtered($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Statistik keseluruhan
        $totalActivities = AuditLog::count();
        $todayActivities = AuditLog::whereDate('created_at', today())->count();
        $createActivities = AuditLog::where('action', 'CREATE')->count();
        $updateActivities = AuditLog::where('action', 'UPDATE')->count();
        $deleteActivities = AuditLog::where('action', 'DELETE')->count();

        $modules = AuditLog::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        // Aktivitas terbanyak per modul
        $moduleStats = AuditLog::query()
            ->select('module', DB::raw('COUNT(*) as total'))
            ->whereNotNull('module')
            ->groupBy('module')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'module');

        // Aktivitas 7 hari terakhir
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());

        $perDay = AuditLog::where('created_at', '>=', $days->first())
            ->pluck('created_at')
            ->groupBy(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->map->count();

        $daily = $days->map(fn ($d) => [
            'label' => $d->translatedFormat('D'),
            'date' => $d->format('d M'),
            'value' => (int) ($perDay[$d->format('Y-m-d')] ?? 0),
        ])->values();

        return view('audit-logs.index', compact(
            'logs',
            'totalActivities',
            'todayActivities',
            'createActivities',
            'updateActivities',
            'deleteActivities',
            'modules',
            'moduleStats',
            'daily'
        ));
    }

    /**
     * Detail satu aktivitas (JSON).
     */
    public function show(string $id)
    {
        $log = AuditLog::findOrFail($id);

        return response()->json($log);
    }

    /**
     * Mengekspor audit log ke CSV, mengikuti filter yang sedang aktif.
     */
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'audit-log-' . now()->format('Y-m-d-His') . '.csv';
        $query = $this->filtered($request);

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 terbaca dengan baik di Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'User ID',
                'Nama Pengguna',
                'Email',
                'Role',
                'Aktivitas',
                'Modul',
                'Nomor Referensi',
                'Deskripsi',
                'Old Values',
                'New Values',
                'IP Address',
                'Tanggal Aktivitas',
            ], ';');

            $query->orderBy('created_at', 'desc')
                ->chunk(500, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            $log->id,
                            $log->user_id,
                            $log->user_name,
                            $log->user_email,
                            $log->user_role,
                            $log->action,
                            $log->module,
                            $log->reference_number,
                            $log->description,
                            json_encode($log->old_values, JSON_UNESCAPED_UNICODE),
                            json_encode($log->new_values, JSON_UNESCAPED_UNICODE),
                            $log->ip_address,
                            $log->created_at,
                        ], ';');
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
