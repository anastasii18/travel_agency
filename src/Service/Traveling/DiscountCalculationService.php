<?php

namespace App\Service\Traveling;


use App\DTO\Traveling;

class DiscountCalculationService
{
    private const PRESCHOOL_DISCOUNT = 0.8;
    private const STUDENT_DISCOUNT = 0.3;
    private const TEENAGE_DISCOUNT = 0.1;
    private const MAX_CHILD_DISCOUNT = 4500;
    private const EARLY_BOOKIG_DISCOUNT = 0.07;
    private const MIDDLE_BOOKING_DISCOUNT = 0.05;
    private const LATE_BOOKING_DISCOUNT = 0.03;
    private const MAX_BOOKING_DISCOUNT = 1500;

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

    private function periodOfStartTravel(Traveling $traveling) : array
    {
        $periodOfTravelStart = [];
        $year = $traveling->dateOfPayment->format('Y');

        //с 1 апреля по 30 сентября следующего года
        if ($traveling->dateOfTravelStart >= new \DateTime("$year-04-01" )&&
            $traveling->dateOfTravelStart <= new \DateTime("$year-09-30 +1 year")) {
            $periodOfTravelStart[] = 1;
        }
        // с 1 октября текущего года по 14 января следующего года
        if ($traveling->dateOfTravelStart >= new \DateTime("$year-10-01") &&
            $traveling->dateOfTravelStart <= new \DateTime("$year-01-14 +1 year")) {
            $periodOfTravelStart[] = 2;
        }
        // с 15 января следующего года и далее
        if ($traveling->dateOfTravelStart >= new \DateTime("$year-01-15 +1 year")) {
            $periodOfTravelStart[] = 3;
        }
        return $periodOfTravelStart;
    }

    private function periodOfPayment(Traveling $traveling) : array
    {
        $periodOfPayment = [];
        $year = $traveling->dateOfPayment->format('Y');

        // ноябрь текущего и ранее
        if ($traveling->dateOfPayment < new \DateTime("$year-12-01")) {
            $periodOfPayment[] = 10;
        }
        // декабрь текущего года
        if ($traveling->dateOfPayment >= new \DateTime("$year-12-01") &&
            $traveling->dateOfPayment < new \DateTime("$year-01-01 +1 year")){
            $periodOfPayment[] = 11;
        }
        // январь следующего года
        if  ($traveling->dateOfTravelStart >= new \DateTime("$year-01-01 +1 year") &&
            $traveling->dateOfTravelStart <  new \DateTime("$year-02-29 +1 year")){
            $periodOfPayment[] = 12;
        }

        // март текущего года и ранее
        if ($traveling->dateOfPayment < new \DateTime("$year-04-01")) {
            $periodOfPayment[] = 20;
        }
        // апрель текущего года
        if ($traveling->dateOfPayment >= new \DateTime("$year-04-01") &&
            $traveling->dateOfPayment < new \DateTime("$year-05-01")){
            $periodOfPayment[] = 21;
        }
        // май текущего года
        if ($traveling->dateOfPayment >= new \DateTime("$year-05-01") &&
            $traveling->dateOfPayment < new \DateTime("$year-06-01")){
            $periodOfPayment[] = 22;
        }

        // август текущего года и ранее
        if ($traveling->dateOfPayment < new \DateTime("$year-09-01")){
            $periodOfPayment[] = 30;
        }
        // сентябрь текущего года
        if ($traveling->dateOfPayment >= new \DateTime("$year-09-01") &&
            $traveling->dateOfPayment <new \DateTime("$year-10-01")){
            $periodOfPayment[] = 31;
        }
        // октябрь текущего года
        if ($traveling->dateOfPayment >= new \DateTime("$year-10-01") &&
            $traveling->dateOfPayment <  new \DateTime("$year-11-01")){
            $periodOfPayment[] = 32;
        }
        return $periodOfPayment;
    }

    private function earlyBookingDiscountCalculation(array $periodOfStartTravel, array $periodOfPayment, float $travelCost) : float
    {
        $discount = [0];

        if ((in_array(10, $periodOfPayment ) && in_array(1, $periodOfStartTravel )) or
            (in_array(20, $periodOfPayment ) && in_array(2, $periodOfStartTravel )) or
            (in_array(30, $periodOfPayment ) && in_array(3, $periodOfStartTravel ))){
            $discount[] = $travelCost * self::EARLY_BOOKIG_DISCOUNT;
        }

        if ((in_array(11, $periodOfPayment ) && in_array(1, $periodOfStartTravel )) or
            (in_array(21, $periodOfPayment ) && in_array(2, $periodOfStartTravel )) or
            (in_array(31, $periodOfPayment ) && in_array(3, $periodOfStartTravel ))){
            $discount[] = $travelCost * self::MIDDLE_BOOKING_DISCOUNT;
        }

        if ((in_array(12, $periodOfPayment ) && in_array(1, $periodOfStartTravel )) or
            (in_array(22, $periodOfPayment ) && in_array(2, $periodOfStartTravel )) or
            (in_array(32, $periodOfPayment ) && in_array(3, $periodOfStartTravel ))){
            $discount[] = $travelCost * self::LATE_BOOKING_DISCOUNT;
        }
        return (max($discount) < self::MAX_BOOKING_DISCOUNT) ? max($discount) : self::MAX_BOOKING_DISCOUNT;
    }

    public function fullDiscountCalculation(Traveling $traveling) : float
    {
        $childDiscount = $this->childDiscountCalculation($traveling);
        $periodOfStartTravel = $this->periodOfStartTravel($traveling);
        $periodOfPayment = $this->periodOfPayment($traveling);

        $travelCostWithChildDiscount = $traveling->travelCost - $childDiscount;

        $earlyBookingDiscount = $this->earlyBookingDiscountCalculation($periodOfStartTravel, $periodOfPayment, $travelCostWithChildDiscount);
        return $traveling->travelCost - $childDiscount - $earlyBookingDiscount;
    }
}