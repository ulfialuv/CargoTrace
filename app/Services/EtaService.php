<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\CarbonInterface;

/**
 * Aturan ETA & delay (deterministik, mudah diuji).
 *   delay = estimated_arrival (ETA terkini) - expected_arrival (jadwal dijanjikan)
 *   delayed jika delay > threshold. Severity ditentukan dari lamanya delay.
 * Selisih dihitung dalam menit penuh (detik diabaikan).
 */
class EtaService
{
    public function rules(): array
    {
        return [
            'threshold'  => (float) config('cargotrace.delay.threshold_hours', 0),
            'low_max'    => (float) config('cargotrace.delay.low_max_hours', 6),
            'medium_max' => (float) config('cargotrace.delay.medium_max_hours', 24),
        ];
    }

    public function classify(float $hours, ?array $rules = null): string
    {
        $rules ??= $this->rules();

        if ($hours <= $rules['low_max']) {
            return 'LOW';
        }

        return $hours <= $rules['medium_max'] ? 'MEDIUM' : 'HIGH';
    }

    /** Evaluasi satu shipment. Null jika tanggal expected/estimated belum lengkap. */
    public function evaluate(?Shipment $shipment): ?array
    {
        if (! $shipment || ! $shipment->estimated_arrival || ! $shipment->expected_arrival) {
            return null;
        }

        $info = $this->evaluateDates($shipment->expected_arrival, $shipment->estimated_arrival);

        if ($shipment->status === 'DELIVERED') {
            $info['applicable'] = false;
            $info['delayed']    = false;
            $info['severity']   = null;
            $info['reason']     = 'Pengiriman sudah DELIVERED; pemantauan keterlambatan dihentikan.';
        }

        return $info;
    }

    /** Inti perhitungan: murni dari dua tanggal + aturan, tanpa database. */
    public function evaluateDates(CarbonInterface $expected, CarbonInterface $estimated, ?array $rules = null): array
    {
        $rules ??= $this->rules();

        $seconds = $estimated->getTimestamp() - $expected->getTimestamp();
        $minutes = $seconds >= 0 ? intdiv($seconds, 60) : -intdiv(-$seconds, 60); // positif = ETA setelah expected
        $hours   = $minutes / 60;

        $delayed  = $minutes > 0 && $hours > $rules['threshold'];
        $severity = $delayed ? $this->classify($hours, $rules) : null;
        $lateMin  = max(0, $minutes);

        $ruleText = sprintf(
            'Delayed jika ETA melewati expected lebih dari %s jam. 0-%s jam LOW; >%s-%s jam MEDIUM; >%s jam HIGH.',
            $this->num($rules['threshold']),
            $this->num($rules['low_max']),
            $this->num($rules['low_max']), $this->num($rules['medium_max']),
            $this->num($rules['medium_max'])
        );

        if ($delayed) {
            $reason = sprintf(
                'ETA %s melewati expected arrival %s selama %s (%s jam) → severity %s.',
                $estimated->format('d M Y H:i'), $expected->format('d M Y H:i'),
                $this->durationText($lateMin), $this->num($hours), $severity
            );
        } elseif ($minutes > 0) {
            $reason = sprintf(
                'ETA terlambat %s jam, belum melewati threshold %s jam → belum dianggap delayed.',
                $this->num($hours), $this->num($rules['threshold'])
            );
        } else {
            $reason = sprintf(
                'ETA %s tidak melewati expected arrival %s → tepat waktu.',
                $estimated->format('d M Y H:i'), $expected->format('d M Y H:i')
            );
        }

        return [
            'applicable'        => true,
            'delayed'           => $delayed,
            'severity'          => $severity,
            'delay_minutes'     => $lateMin,
            'delay_hours'       => round($lateMin / 60, 2),
            'difference_hours'  => round($hours, 2),
            'expected_arrival'  => $expected->toIso8601String(),
            'estimated_arrival' => $estimated->toIso8601String(),
            'threshold_hours'   => $rules['threshold'],
            'rule'              => $ruleText,
            'reason'            => $reason,
        ];
    }

    private function durationText(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
    }

    private function num(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') ?: '0';
    }
}
