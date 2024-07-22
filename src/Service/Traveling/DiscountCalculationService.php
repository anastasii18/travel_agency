<?php

namespace App\Service\Traveling;


use App\DTO\Traveling;
use Carbon\Carbon;

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
    private const PRESCHOOL_YEARS_MIN = 3;
    private const PRESCHOOL_YEARS_MAX = 6;
    private const STUDENT_YEARS_MAX = 12;
    private const TEENAGE_YEARS_MAX =  18;


    private function childAgeCalculation(Traveling $traveling) : int
    {
        return $traveling->getDateOfBirth()->diffInYears($traveling->getDateOfTravelStart());
    }

    private function childDiscountCalculation(Traveling $traveling) : float
    {
        $age = $this->childAgeCalculation($traveling);
        $discount = 0;

        switch ($age) {
            case ($age >= self::PRESCHOOL_YEARS_MIN && $age < self::PRESCHOOL_YEARS_MAX):
                $discount = $traveling->getTravelCost() * self::PRESCHOOL_DISCOUNT;
                break;
            case ($age >= self::PRESCHOOL_YEARS_MAX && $age < self::STUDENT_YEARS_MAX):
                $discount = ($traveling->getTravelCost() * self::STUDENT_DISCOUNT < self::MAX_CHILD_DISCOUNT) ?
                    $traveling->getTravelCost() * self::STUDENT_DISCOUNT : self::MAX_CHILD_DISCOUNT;
                break;
            case ($age >= self::STUDENT_YEARS_MAX && $age < self::TEENAGE_YEARS_MAX):
                $discount = $traveling->getTravelCost() * self::TEENAGE_DISCOUNT;
                break;
            default:
                break;
        }

        return $discount;
    }

    private function isDateInRange(Carbon $date, Carbon $start, Carbon $end): bool
    {
        return $date >= $start && $date < $end;
    }

    private function periodOfStartTravel(Traveling $traveling) : array
    {
        $periodOfTravelStart = [];
        $year = $traveling->getDateOfPayment()->format('Y');

        //с 1 апреля по 30 сентября следующего года
        if  ($this->isDateInRange($traveling->getDateOfTravelStart(), Carbon::create($year, 4, 1, 0 ), Carbon::create($year, 10, 1, 0)->addYear())) {
            $periodOfTravelStart[] = 1;
        }
        // с 1 октября текущего года по 14 января следующего года
        if ($this->isDateInRange($traveling->getDateOfTravelStart(),Carbon::create($year, 10, 1, 0 ),Carbon::create($year, 1, 15, 0)->addYear())) {
            $periodOfTravelStart[] = 2;
        }
        // с 15 января следующего года и далее
        if ($traveling->getDateOfTravelStart() >= Carbon::create($year, 1, 15, 0)->addYear()) {
            $periodOfTravelStart[] = 3;
        }
        return $periodOfTravelStart;
    }

    private function periodOfPayment(Traveling $traveling) : array
    {
        $periodOfPayment = [];
        $year = $traveling->getDateOfPayment() ->format('Y');

        // ноябрь текущего и ранее
        if ($traveling->getDateOfPayment()  < Carbon::create($year, 12, 1, 0)) {
            $periodOfPayment[] = 10;
        }
        // декабрь текущего года
        if ($this->isDateInRange($traveling->getDateOfPayment(), Carbon::create($year, 12, 1, 0), Carbon::create($year, 1, 1, 0)->addYear())){
            $periodOfPayment[] = 11;
        }
        // январь следующего года
        if  ($this->isDateInRange($traveling->getDateOfPayment(), Carbon::create($year, 1, 1, 0)->addYear(), Carbon::create($year, 2, 29, 0)->addYear())){
            $periodOfPayment[] = 12;
        }

        // март текущего года и ранее
        if ($traveling->getDateOfPayment()  < Carbon::create($year, 4, 1, 0)) {
            $periodOfPayment[] = 20;
        }
        // апрель текущего года
        if ($this->isDateInRange($traveling->getDateOfPayment(),Carbon::create($year, 4, 1, 0), Carbon::create($year, 5, 1, 0))){
            $periodOfPayment[] = 21;
        }
        // май текущего года
        if ($this->isDateInRange($traveling->getDateOfPayment(),Carbon::create($year, 9, 5, 1), Carbon::create($year, 6, 1, 0))){
            $periodOfPayment[] = 22;
        }

        // август текущего года и ранее
        if ($traveling->getDateOfPayment() < Carbon::create($year, 9, 1, 0)){
            $periodOfPayment[] = 30;
        }
        // сентябрь текущего года
        if ($this->isDateInRange($traveling->getDateOfPayment(),Carbon::create($year, 9, 1, 0), Carbon::create($year, 10, 1, 0))){
            $periodOfPayment[] = 31;
        }
        // октябрь текущего года
        if ($this->isDateInRange($traveling->getDateOfPayment(),Carbon::create($year, 10, 1, 0), Carbon::create($year, 11, 1, 0))){
            $periodOfPayment[] = 32;
        }
        return $periodOfPayment;
    }

    private function isPeriodInArray($array, $periodOfPayment, $periodOfStartTravel): bool
    {
        if ((in_array($array[0], $periodOfPayment ) && in_array(1, $periodOfStartTravel )) ||
            (in_array($array[1], $periodOfPayment ) && in_array(2, $periodOfStartTravel )) ||
            (in_array($array[2], $periodOfPayment ) && in_array(3, $periodOfStartTravel )))
            return true;

        return false;
    }

    private function earlyBookingDiscountCalculation(array $periodOfStartTravel, array $periodOfPayment, float $travelCost) : float
    {
        $discount = [0];

        if ($this->isPeriodInArray([10,20,30], $periodOfPayment, $periodOfStartTravel)){
            $discount[] = $travelCost * self::EARLY_BOOKIG_DISCOUNT;
        }

        if ($this->isPeriodInArray([11,21,31], $periodOfPayment, $periodOfStartTravel)){
            $discount[] = $travelCost * self::MIDDLE_BOOKING_DISCOUNT;
        }

        if ($this->isPeriodInArray([12,22,32], $periodOfPayment, $periodOfStartTravel)){
            $discount[] = $travelCost * self::LATE_BOOKING_DISCOUNT;
        }
        return (max($discount) < self::MAX_BOOKING_DISCOUNT) ? max($discount) : self::MAX_BOOKING_DISCOUNT;
    }

    public function fullDiscountCalculation(Traveling $traveling) : float
    {
        $childDiscount = $this->childDiscountCalculation($traveling);
        $travelCostWithChildDiscount = $traveling->getTravelCost() - $childDiscount;

        if(!$traveling->getDateOfPayment()){
            return $travelCostWithChildDiscount;
        }

        $periodOfStartTravel = $this->periodOfStartTravel($traveling);
        $periodOfPayment = $this->periodOfPayment($traveling);
        $earlyBookingDiscount = $this->earlyBookingDiscountCalculation($periodOfStartTravel, $periodOfPayment, $travelCostWithChildDiscount);

        return $travelCostWithChildDiscount - $earlyBookingDiscount;
    }
}