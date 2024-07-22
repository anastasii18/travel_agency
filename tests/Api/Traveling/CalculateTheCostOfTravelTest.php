<?php
namespace App\Tests\Api\Traveling;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CalculateTheCostOfTravelTest extends WebTestCase
{

    public function testWithEarlyBookingDiscount(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST,
            'http://localhost:8001/cost_of_travel',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['travelCost' => 555,
                'dateOfBirth' => '2000-01-02',
                'dateOfTravelStart' => '2027-05-01',
                'dateOfPayment' => '2026-11-30'])
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame(516.15, $content['resultTravelCost']);
        restore_exception_handler();
    }

    public function testWithMiddleBookingDiscount(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST,
            'http://localhost:8001/cost_of_travel',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['travelCost' => 7890,
                'dateOfBirth' => '2000-01-02',
                'dateOfTravelStart' => '2027-05-01',
                'dateOfPayment' => '2026-12-30'])
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame(7495.5, $content['resultTravelCost']);
        restore_exception_handler();
    }

    public function testWithEarlyBookingAndChildDiscount(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST,
            'http://localhost:8001/cost_of_travel',
            server : ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['travelCost' => 9988,
                'dateOfBirth' => '2017-01-02',
                'dateOfTravelStart' => '2027-05-01',
                'dateOfPayment' => '2026-11-30'])
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertSame(6502.188, $content['resultTravelCost']);
        restore_exception_handler();
    }

    public function testWithErrorDateOfBirth(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST,
            'http://localhost:8001/cost_of_travel',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['travelCost' => 32500,
                'dateOfTravelStart' => '2025-01-16',
                'dateOfPayment' => '2025-09-15'])
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Field dateOfBirth is required!!", json_decode($response->getContent())->error);
        restore_exception_handler();
    }

    public function testWithErrorTravelCost(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST,
            'http://localhost:8001/cost_of_travel',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['dateOfBirth' => '2025-01-16',
                'dateOfTravelStart' => '2025-01-16',
                'dateOfPayment' => '2025-09-15'])
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Field travelCost is required!!", json_decode($response->getContent())->error);
        restore_exception_handler();
    }

}
