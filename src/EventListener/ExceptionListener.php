<?php

namespace App\EventListener;

use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    private function checkValidateDate($array, $event, $parameterName): bool
    {
        if(!array_key_exists($parameterName, $array))
            return true;
        try {
            new Carbon($array[$parameterName]);
        } catch (Exception $e) {
            $response = new JsonResponse(['error' => "The $parameterName has the wrong type!!!"], 400);
            $event->setResponse($response);
            return false;
        }
        return true;
    }

    private function checkValidateTravelCost($array, $event): bool{
        if (is_float($array['travelCost']) ||is_int ($array['travelCost'])) {
            return true;
        } else {
            $response = new JsonResponse(['error' => "The travelCost has the wrong type!!!"], 400);
            $event->setResponse($response);
            return false;
        }
    }

    private function checkRequiredField($array, $event, $parameterName): bool
    {
        if (!array_key_exists($parameterName, $array)){
            $response = new JsonResponse(['error' => "Field $parameterName is required!!"], 400);
            $event->setResponse($response);
            return false;
        }
        return true;
    }

    public function onKernelException(ExceptionEvent $event): void
    {

        $content = $event->getRequest()->getContent();
        $array = json_decode($content, true);
        if($event->getRequest()->getPathInfo() === '/cost_of_travel'){

            if(!$this->checkRequiredField($array, $event,'travelCost') ||
                !$this->checkRequiredField($array, $event,'dateOfBirth') ||
                !$this->checkValidateTravelCost($array, $event) ||
                !$this->checkValidateDate($array, $event, 'dateOfBirth') ||
                !$this->checkValidateDate($array, $event, 'dateOfTravelStart') ||
                !$this->checkValidateDate($array, $event, 'dateOfPayment'))
                return;
        }

        $response = new JsonResponse(['error' => 'An error occurred'], 500);
        $event->setResponse($response);
    }
}