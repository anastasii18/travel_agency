<?php

namespace App\Service\Traveling;


use App\DTO\Traveling;

class DiscountCalculationService
{
    private const PRESCHOOL_DISCOUNT = 0.8;
    private const STUDENT_DISCOUNT = 0.3;
    private const TEENAGE_DISCOUNT = 0.1;
    private const MAX_CHILD_DISCOUNT = 4500;

    private function childAgeCalculation(Traveling $traveling) : int
    {
        $interval = $traveling->dateOfBirth->diff($traveling->dateOfTravelStart);
        return  $interval->y;
    }

    private function childDiscountCalculation(Traveling $traveling) : float
    {
        $age = $this->childAgeCalculation($traveling);
        $discount = 0;

        switch ($age) {
            case ($age >= 3 && $age < 6):
                $discount = $traveling->travelCost * self::PRESCHOOL_DISCOUNT;
                break;
            case ($age >= 6 && $age < 12):
                $discount = ($traveling->travelCost * self::STUDENT_DISCOUNT < self::MAX_CHILD_DISCOUNT) ?
                    $traveling->travelCost * self::STUDENT_DISCOUNT : self::MAX_CHILD_DISCOUNT;
                break;
            case ($age >= 12 && $age < 18):
                $discount = $traveling->travelCost * self::TEENAGE_DISCOUNT;
                break;
            default:
                break;
        }

        return $discount;
    }


    public function fullDiscountCalculation(Traveling $traveling) : float
    {
        $childDiscount = $this->childDiscountCalculation($traveling);

        //TODO: calculate $earlyBookingDiscount
        $earlyBookingDiscount = 0;
        return $traveling->travelCost - $childDiscount - $earlyBookingDiscount;
    }
}