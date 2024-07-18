<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $content = $event->getRequest()->getContent();
        $array = json_decode($content, true);

        if (!array_key_exists('travelCost', $array)){
            $response = new JsonResponse(['error' => 'Field travelCost is required!!'], 500);
            $event->setResponse($response);
            return;
        }
        elseif (!array_key_exists('dateOfBirth', $array)){
            $response = new JsonResponse(['error' => 'Field dateOfBirth is required!!'], 500);
            $event->setResponse($response);
            return;
        }

        $response = new JsonResponse(['error' => 'An error occurred'], 500);
        $event->setResponse($response);
    }
}