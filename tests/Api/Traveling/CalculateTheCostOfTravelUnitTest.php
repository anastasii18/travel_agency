<?php

namespace App\Tests\Api\Traveling;

use App\DTO\Traveling;
use App\Service\Traveling\DiscountCalculationService;
use PHPUnit\Framework\TestCase;


class CalculateTheCostOfTravelUnitTest extends TestCase
{
    private DiscountCalculationService $discountCalculationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountCalculationService = new DiscountCalculationService();
    }

    public function testWithEarlyBookingDiscount(): void
    {
        $traveling = new Traveling(555, '2000-01-02', '2027-05-01', '2026-11-30');
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals(516.15, $resultTravelCost);
    }

    public function testWithMiddleBookingDiscount(): void
    {
        $traveling = new Traveling(7890, '2000-01-02');
        self::assertTrue((bool)$traveling->getTravelCost());

        self::assertFalse((bool)$traveling->getDateOfPayment());
        $traveling->setDateOfPayment('2026-12-30');
        self::assertInstanceOf(Traveling::class, $traveling);
        self::assertTrue((bool)$traveling->getDateOfPayment());

        $traveling->setDateOfTravelStart( '2027-05-01');

        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals(7495.5, $resultTravelCost);
    }

    public function testWithEarlyBookingAndChildDiscount(): void
    {
        $traveling = new Traveling(9988, '2017-01-02',  '2027-05-01', '2026-11-30');
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals(6502.188, $resultTravelCost);
    }
}