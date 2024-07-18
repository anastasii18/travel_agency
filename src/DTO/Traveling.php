<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Traveling
{
    #[Assert\NotBlank]
    #[Assert\Type("float")]
    public float $travelCost;

    #[Assert\NotBlank]
    #[Assert\Type("\DateTime")]
    public \DateTime $dateOfBirth;

    #[Assert\Type("\DateTime")]
    public \DateTime $dateOfTravelStart;

    #[Assert\Type("\DateTime")]
    public \DateTime $dateOfPayment;
}