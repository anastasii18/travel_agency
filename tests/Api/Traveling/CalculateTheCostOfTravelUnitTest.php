<?php

namespace App\Tests\Api\Traveling;

use App\DTO\Traveling;
use App\Service\Traveling\DiscountCalculationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


class CalculateTheCostOfTravelUnitTest extends TestCase
{
    private DiscountCalculationService $discountCalculationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountCalculationService = new DiscountCalculationService();
    }

    public static function childDiscountProvider(): array
    {
        return [
            'Less than 3 years - 0%' => [555, '2026-01-02', '2027-12-01', null, 555,],
            '3 years - 80%' => [555, '2024-12-01', '2027-12-01', null, 111,],
            '5 years - 80%' => [555, '2022-12-01', '2027-12-01', null, 111,],
            '6 years - 30%' => [555, '2021-12-01', '2027-12-01', null, 388.5,],
            '6 years - 30% (max - 4500)' => [16000, '2021-12-01', '2027-12-01', null, 11500,],
            '12 years - 10%' => [16000, '2015-12-01', '2027-12-01', null, 14400,],
            '17 years - 10%' => [16000, '2010-12-01', '2027-12-01', null, 14400,],
            '18 years - 0%' => [16000, '2009-12-01', '2027-12-01', null, 16000,],
        ];
    }

    #[Test]
    #[DataProvider('childDiscountProvider')]
    public function testChildDiscount(float $travelCost, string $dateOfBirth, string $dateOfTravelStart = null, string $dateOfPayment = null, float $expectedResult): void
    {
        $traveling = new Traveling($travelCost, $dateOfBirth, $dateOfTravelStart, $dateOfPayment);
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals($expectedResult, $resultTravelCost);
    }

    public static function firstPeriodOfStartDateProvider(): array
    {
        return [
            'November this year and earlie - 7% (max - 1500)' => [25000,  '2000-01-01', '2028-05-01', '2027-10-03', 23500],
            'November this year and earlier - 7%' => [13456, '2000-01-01', '2028-05-01', '2027-10-03', 12514.08],
            'December this year - 5%' => [13456, '2000-01-01', '2028-05-01', '2027-12-08', 12783.2],
        ];
    }

    #[Test]
    #[DataProvider('firstPeriodOfStartDateProvider')]
    public function testFirstPeriodOfStartDate(float $travelCost, string $dateOfBirth, string $dateOfTravelStart = null, string $dateOfPayment = null, float $expectedResult): void
    {
        $traveling = new Traveling($travelCost, $dateOfBirth, $dateOfTravelStart, $dateOfPayment);
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals($expectedResult, $resultTravelCost);
    }

    public static function secondPeriodOfStartDateProvider(): array
    {
        return [
            'March this year - 7% (max - 1500)' => [25000, '2000-01-01', '2025-11-01', '2025-03-09', 23500],
            'March this year - 7%' => [6000, '2000-01-01', '2025-11-01', '2025-03-09', 5580],
            'April this year - 5%' => [6000, '2000-01-01', '2025-11-01', '2025-04-08', 5700],
            'May this year - 3%' => [6000, '2000-01-01', '2025-11-01', '2025-05-08', 5820],
        ];
    }

    #[Test]
    #[DataProvider('secondPeriodOfStartDateProvider')]
    public function testSecondPeriodOfStartDate(float $travelCost, string $dateOfBirth, string $dateOfTravelStart = null, string $dateOfPayment = null, float $expectedResult): void
    {
        $traveling = new Traveling($travelCost, $dateOfBirth, $dateOfTravelStart, $dateOfPayment);
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals($expectedResult, $resultTravelCost);
    }

    public static function thirdPeriodOfStartDateProvider(): array
    {
        return [
            'August this year - 7% (max - 1500)' => [25000, '2000-01-01', '2030-01-15', '2029-08-09', 23500],
            'August this year- 7%' => [8999, '2000-01-01', '2030-01-15', '2029-08-09', 8369.07],
            'September this year - 5%' => [8999, '2000-01-01', '2030-01-15', '2029-09-10', 8549.05],
            'October this year - 3%' => [8999, '2000-01-01', '2030-01-15', '2029-10-08', 8729.03],
        ];
    }

    #[Test]
    #[DataProvider('thirdPeriodOfStartDateProvider')]
    public function testThirdPeriodOfStartDate(float $travelCost, string $dateOfBirth, string $dateOfTravelStart = null, string $dateOfPayment = null, float $expectedResult): void
    {
        $traveling = new Traveling($travelCost, $dateOfBirth, $dateOfTravelStart, $dateOfPayment);
        self::assertInstanceOf(Traveling::class, $traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        self::assertEquals($expectedResult, $resultTravelCost);
    }
}