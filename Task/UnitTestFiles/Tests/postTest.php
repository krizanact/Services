<?php

namespace UnitTestFiles\Tests;
use PHPUnit\Framework\TestCase;
use Requests;           


class postTest extends TestCase
{


    public function testTrueAssetsToTrue()
    {

        $headers = array('Accept' => 'application/json');
        $option1 =  array   ('amount' => 'aa', 'currency' => 'EUR');   // amount data wrong format ,but currency correct
        $option2 = array ('amount' => '123', 'currency' => 'aaa');     //amount data correct ,but currency wrong
        $option3 = array ('amount' => 'zz', 'currency' => 'aaa');      //both data wrong


            $request1 = Requests::post('http://localhost/task/serviceA.php', $headers, $option1);
            $status1 = json_decode($request1->status_code);

        $request2 = Requests::post('http://localhost/task/serviceA.php', $headers, $option2);
        $status2 = json_decode($request2->status_code);

        $request3 = Requests::post('http://localhost/task/serviceA.php', $headers, $option3);
        $status3 = json_decode($request3->status_code);

            //checking if POST request is invalid( 400 status )
            //if we send wrong data as amount or currency we will equal 400 response code
        
            $this->assertEquals(400, $status1);
            $this->assertEquals(400, $status2);
            $this->assertEquals(400, $status3);



    }

}


