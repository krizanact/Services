<?php

namespace UnitTestFiles\Tests;
use PHPUnit\Framework\TestCase;
use Requests;           


class post2Test extends TestCase
{


    public function testTrueAssetsToTrue()
    {


        $headers = array('Content-Type' => 'application/json');
        $data = array('amount' => '123', 'currency' => 'EUR');
        $response = Requests::post('http://localhost/task/serviceA.php', $headers, json_encode($data));

        $status = json_decode($response->status_code);


     $this->assertEquals(200, $status);  //checking if POST request returns 200 status (if both data is correct)





    }

}

