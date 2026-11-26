<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

/** Pemasukan di dashboard harus sama dengan "Pendapatan Lunas" di halaman Pengiriman. */
class DashboardIncomeTest extends TestCase
{
    use RefreshDatabase;

    private function income(): array
    {
        $method = new ReflectionMethod(DashboardController::class, 'buildIncome');
        $method->setAccessible(true);

        return $method->invoke(app(DashboardController::class), now());
    }

    private function createPaymentsTable(): void
    {
        Schema::create('shipment_payments', function ($t) {
            $t->id();
            $t->unsignedBigInteger('shipment_id');
            $t->string('invoice_number', 40)->unique();
            $t->string('method', 20);
            $t->unsignedBigInteger('subtotal');
            $t->unsignedBigInteger('tax');
            $t->unsignedBigInteger('fee');
            $t->unsignedBigInteger('total');
            $t->string('status', 12);
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
    }

    private function payment(int $n, int $total, string $status, $paidAt): void
    {
        DB::table('shipment_payments')->insert([
            'shipment_id'    => $n,
            'invoice_number' => 'INV-' . $n,
            'method'         => 'VA',
            'subtotal'       => $total,
            'tax'            => 0,
            'fee'            => 0,
            'total'          => $total,
            'status'         => $status,
            'paid_at'        => $paidAt,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function test_income_sums_only_paid_payments(): void
    {
        $this->createPaymentsTable();
        $this->payment(1, 100000, 'PAID', now());
        $this->payment(2, 158333, 'PAID', now()->subMonthNoOverflow());
        $this->payment(3, 50000, 'PENDING', null);
        $this->payment(4, 70000, 'REFUNDED', now());

        $income = $this->income();

        $this->assertTrue($income['available']);
        $this->assertSame('shipment_payments.total', $income['column']);
        $this->assertEqualsWithDelta(258333, array_sum(array_column($income['years'], 't')), 0.01);

        $today = collect($income['daily'])->firstWhere('k', now()->format('Y-m-d'));
        $this->assertEqualsWithDelta(100000, $today['t'], 0.01);
        $this->assertSame(1, $today['c']);
    }

    public function test_income_is_zero_but_available_when_nothing_is_paid_yet(): void
    {
        $this->createPaymentsTable();
        $this->payment(1, 50000, 'PENDING', null);

        $income = $this->income();

        $this->assertTrue($income['available']);
        $this->assertSame([], $income['daily']);
        $this->assertSame([], $income['years']);
    }

    public function test_falls_back_to_the_old_method_when_payment_table_does_not_exist(): void
    {
        $income = $this->income();

        $this->assertFalse($income['available']); // tidak ada kolom harga di tabel shipments
    }
}
