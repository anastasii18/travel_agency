<?php

namespace App\Controller\Traveling;

use App\DTO\Traveling;
use App\Service\Traveling\DiscountCalculationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculateTheCostOfTravel extends AbstractController
{

    public function __construct(
        private DiscountCalculationService $discountCalculationService,
        private ValidatorInterface  $validator
    )
    {
    }

    #[Route('/cost_of_travel', name: 'api_cost_of_travel', methods: ['POST'], format: 'json')]
    public function calculateTheCostOfTravel(#[MapRequestPayload] Traveling $traveling): Response
    {
        $this->validator->validate($traveling);
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        return new JsonResponse(['resultTravelCost' => $resultTravelCost]);
    }

}