<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiProductControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $headers = ['X-Current-Time' => '2021-01-01', 'Accept' => 'application/json'];
        $client = static::createClient();
        $client->request('GET', 'http://store.loc/api/product', [], [], $headers);
        $this->assertResponseIsSuccessful();
        $this->assertTrue(is_array(json_decode($client->getResponse()->getContent(), true)));
    }


    public function testAddProduct():void
    {
        $headers = ['X-Current-Time' => '2021-01-01', 'Accept' => 'application/json'];
        $postData = ['name' => 'Правый носок'];
        $client = static::createClient();
        $client->request(
            'POST',
            'http://store.loc/api/product',
            [],
            [],
            $headers,
            json_encode($postData)
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('id', json_decode($client->getResponse()->getContent(), true));
    }

}
