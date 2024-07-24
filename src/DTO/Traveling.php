<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Carbon\Carbon;

class Traveling
{
    #[Assert\NotBlank]
    #[Assert\Type("float")]
    protected float $travelCost;

    #[Assert\NotBlank]
    #[Assert\Type(Carbon::class)]
    protected Carbon $dateOfBirth;

    #[Assert\Type(Carbon::class)]
    protected Carbon $dateOfTravelStart;

    #[Assert\Type(Carbon::class)]
    protected ?Carbon $dateOfPayment = null;

    public function __construct( $travelCost,  $dateOfBirth,  $dateOfTravelStart = null,  $dateOfPayment = null)
    {
        $this->setTravelCost($travelCost);
        $this->setDateOfBirth($dateOfBirth);
        $this->setDateOfTravelStart($dateOfTravelStart);
        $this->setDateOfPayment($dateOfPayment);
    }
    public function getTravelCost(): ?float
    {
        return $this->travelCost;
    }

    public function getDateOfBirth(): ?Carbon
    {
        return $this->dateOfBirth;
    }

    public function getDateOfTravelStart(): ?Carbon
    {
        return $this->dateOfTravelStart;
    }

    public function getDateOfPayment(): ?Carbon
    {
        return $this->dateOfPayment;
    }

    public function setTravelCost(float $travelCost): void
    {
        $this->travelCost = $travelCost;
    }

    public function setDateOfBirth(string $dateOfBirth): void
    {
        $this->dateOfBirth = new Carbon($dateOfBirth);
    }

    public function setDateOfTravelStart(?string $dateOfTravelStart): void
    {
        $this->dateOfTravelStart = $dateOfTravelStart ? new Carbon($dateOfTravelStart) : Carbon::now();
    }

    public function setDateOfPayment(?string $dateOfPayment): void
    {
        $this->dateOfPayment = $dateOfPayment ? new Carbon($dateOfPayment) : null;
    }

}