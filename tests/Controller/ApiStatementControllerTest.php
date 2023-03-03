<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiStatementControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $headers = ['X-Current-Time' => '2021-01-01', 'Accept' => 'application/json'];
        $client = static::createClient();
        $client->request('GET', 'http://store.loc/api/statement', [], [], $headers);
        $this->assertResponseIsSuccessful();
        $this->assertTrue(is_array(json_decode($client->getResponse()->getContent(), true)));
    }

    /**
     * нормальное добавление проводки
     */
    public function testAddIncome():void
    {
        $headers = ['X-Current-Time' => '2021-01-01', 'Accept' => 'application/json'];
        $postData = [
            'product_id' => 1,
            'amount'    => 10,
            'cost'      => 100,
            'document_prop' => 'TestPost '.time()
        ];
        $client = static::createClient();
        $client->request(
            'POST',
            'http://store.loc/api/statement/income',
            [],
            [],
            $headers,
            json_encode($postData)
        );
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('id', json_decode($client->getResponse()->getContent(), true));
    }

    /**
     * Добавление провдки, вызывающее ошибку
     */
    public function testAddIncomeFalseProduct():void
    {
        $headers = ['X-Current-Time' => '2021-01-01', 'Accept' => 'application/json'];
        $postData = [
            'product_id' => time(),
            'amount'    => 10,
            'cost'      => 100,
            'document_prop' => 'TestPost '.time()
        ];
        $client = static::createClient();
        $client->request(
            'POST',
            'http://store.loc/api/statement/income',
            [],
            [],
            $headers,
            json_encode($postData)
        );
        $res = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(), 'Выброшено исключение при создании проводки');
        $this->assertIsNotArray($res);
    }


}
