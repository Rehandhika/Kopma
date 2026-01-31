<?php

namespace Tests\Unit;

use App\Services\ShuPointService;
use PHPUnit\Framework\TestCase;

class ShuPointServiceTest extends TestCase
{
    public function test_compute_earned_points_returns_zero_when_percentage_zero(): void
    {
        $service = new ShuPointService();
        $this->assertSame(0, $service->computeEarnedPoints(10000, 0));
    }

    public function test_compute_earned_points_uses_basis_points_and_floors(): void
    {
        $service = new ShuPointService();
        $this->assertSame(100, $service->computeEarnedPoints(10000, 100));
        $this->assertSame(2, $service->computeEarnedPoints(99, 250));
        $this->assertSame(0, $service->computeEarnedPoints(1, 1));
    }

    public function test_compute_earned_points_caps_percentage_at_100_percent(): void
    {
        $service = new ShuPointService();
        $this->assertSame(10000, $service->computeEarnedPoints(10000, 20000));
    }
}

