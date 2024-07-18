<?php

namespace App\Controller\Traveling;

use App\DTO\Traveling;
use App\Service\Traveling\DiscountCalculationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculateTheCostOfTravel extends AbstractController
{

    public function __construct(
        private DiscountCalculationService $discountCalculationService
    )
    {
    }

    #[Route('/cost_of_travel', name: 'api_cost_of_travel', methods: ['POST'])]
    public function calculateTheCostOfTravel(Request $request): Response
    {
        $data = $request->toArray();
        $traveling = new Traveling();

        $traveling->travelCost = $data['travelCost'] ?? null;
        $traveling->dateOfBirth = isset($data['dateOfBirth']) ? new \DateTime($data['dateOfBirth']) : null;
        $traveling->dateOfTravelStart = isset($data['dateOfTravelStart']) ? new \DateTime($data['dateOfTravelStart']) : new \DateTime();
        $traveling->dateOfPayment = isset($data['dateOfPayment']) ? new \DateTime($data['dateOfPayment']) : null;

        $resultTravelCost = $this->discountCalculationService->fullDiscountCalculation($traveling);

        return new JsonResponse(['resultTravelCost' => $resultTravelCost]);
    }

}