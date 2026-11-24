<?php

namespace Tests\Unit;

use App\Services\EtaService;
use App\Services\ExceptionService;
use App\Services\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackingServiceTest extends TestCase
{
    private function service(): TrackingService
    {
        return new TrackingService(new ExceptionService(new EtaService()));
    }

    public function test_next_status_follows_the_lifecycle(): void
    {
        $service = $this->service();

        $this->assertSame('BOOKED', $service->nextOf('CREATED'));
        $this->assertSame('PICKED_UP', $service->nextOf('BOOKED'));
        $this->assertSame('IN_TRANSIT', $service->nextOf('PICKED_UP'));
        $this->assertSame('DELIVERED', $service->nextOf('IN_TRANSIT'));
    }

    public function test_delivered_and_unknown_status_have_no_next(): void
    {
        $service = $this->service();

        $this->assertNull($service->nextOf('DELIVERED'));
        $this->assertNull($service->nextOf('NGAWUR'));
    }

    public function test_lifecycle_has_five_ordered_statuses(): void
    {
        $this->assertSame(
            ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'],
            TrackingService::STATUSES
        );
    }
}
