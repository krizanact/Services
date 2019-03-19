<?php

namespace UnitTestFiles\Tests;
use PHPUnit\Framework\TestCase;
use Requests;           


class getTest extends TestCase
{


    public function testTrueAssetsToTrue()
    {


        $request = Requests::get('http://localhost/task/State.php');

        $status = json_decode($request->status_code);

         $this->assertEquals(200, $status);  //checking if GET request returns 200 status

        }

}


