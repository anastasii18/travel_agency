<?php

namespace App\Controller\Traveling;

use App\DTO\Traveling;
use App\Service\Traveling\DiscountCalculationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CalculateTheCostOfTravel extends AbstractController
{

    public function __construct(
        private DiscountCalculationService $discountCalculationService
    )
    {
    }

    #[Route('/cost_of_travel', name: 'api_cost_of_travel', methods: ['POST'], format: 'json')]
    public function calculateTheCostOfTravel(#[MapRequestPayload] Traveling $traveling): Response
    {
        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);
        return new JsonResponse(['resultTravelCost' => $resultTravelCost]);
    }

}