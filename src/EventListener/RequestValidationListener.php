<?php

namespace App\EventListener;

use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestValidationListener
{
    private function checkValidateDate($array, $event, $parameterName): bool
    {
        if(!array_key_exists($parameterName, $array))
            return true;
        try {
            new Carbon($array[$parameterName]);
        } catch (Exception $e) {
            $response = new JsonResponse(['error' => "The $parameterName has the wrong type!!!"], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return false;
        }
        return true;
    }

    private function checkValidateTravelCost($array, $event): bool{
        if (is_float($array['travelCost']) || is_int($array['travelCost'])) {
            return true;
        } else {
            $response = new JsonResponse(['error' => "The travelCost has the wrong type!!!"], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return false;
        }
    }

    private function checkRequiredField($array, $event, $parameterName): bool
    {
        if (!array_key_exists($parameterName, $array)){
            $response = new JsonResponse(['error' => "Field $parameterName is required!!"], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return false;
        }
        return true;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();

        if ($pathInfo === '/cost_of_travel' || $request->getMethod() === 'POST') {
            $content = $request->getContent();
            $array = json_decode($content, true);

            if (!$this->checkRequiredField($array, $event, 'travelCost') ||
                !$this->checkRequiredField($array, $event, 'dateOfBirth') ||
                !$this->checkValidateTravelCost($array, $event) ||
                !$this->checkValidateDate($array, $event, 'dateOfBirth') ||
                !$this->checkValidateDate($array, $event, 'dateOfTravelStart') ||
                !$this->checkValidateDate($array, $event, 'dateOfPayment'))
                return;
        }
    }
}