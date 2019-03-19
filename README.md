## Theme

   Ultimate goal in this task was to create two services(A and B) and connect them with messaging system to a single business process.
   
## Introduction (setup)

   I worked this task using <a href="https://www.apachefriends.org/index.html">Xampp</a> tool so folder "Task" should be placed inside "C:/xampp/htdocs". After running Apache and MySQL, inside "Task" directory run  
    
        composer install
    
so composer will renew all required and missing data in this project. For connecting to database configure your data inside these two scripts: "ServiceB.php" and "State.php" script. Example:

        $servername = "localhost";
        $username = "root";
        $password = "";
        $db = "task";
	
  File "task.sql" should be imported in MySQL and then we are ready to start.	


## Service A and Service B with HTTP API

  There are three main scripts in this task and that scripts are : "ServiceA.php", "ServiceB.php" and "State.php" so I will start by explaining these scripts.
  For generating AMQP messages I've used RabbitMq and PHP-AMQP library. To connect to RabbitMq  I used data I got from <a href="https://www.cloudamqp.com/">CloudAMQP</a>: 
  
        $host = "moose.rmq.cloudamqp.com";
        $port = "5672";
        $user = "hzamycrw";
        $password = "vLSC__bFVwtFHW-0p2avNvasqB9j6Kj0";
        $vhost = "hzamycrw";
        $exchange = 'router';
        $queue = 'msgs';
        
  After successfully making relationship beetween sender and receiver(Service A and Service B) next thing was to make sucessfully POST request to Service A and I've sent requests with <a href="https://www.getpostman.com/">Postman</a>(API tester). So the goal was to send json message with  parameters "Amount" and "Curreny" to Service A and Service A had to accept that message. After payload came to Service A there are specific conditions to be filled defined in IF statement and if all condtions are met message will be sent to Service B and  response 200 will be generated or if one of conditions isn't met message won't be sent to Service B and response 400(Invalid request) will be generated. We can run our receiver in console typing:
        
       php serviceB.php
       
so for each message that is successfully sent will be shown here and for each received message function

       update_data($amount,$currency);
       
will update our new transaction to database with increasing or decreasing the balance. POST request/s should be sent to url:

       http://localhost/task/serviceA.php
       
and content-type should be application/json and sent in format like this(example):

       {
	      "amount":"56345.64",
	      "currency":"EUR"
       } 

  So as I already mentioned things above Service B will consume every message that is being successfully sent from Service A and as a result of receiving function will make database changes even if Service B is offline, all messages that have been sent in meanwhile will come as soon as Service B becomes online and will then process as usual.
  
  Script "State.php" is seperated code, if we want to check our current state(balance,updated_at) we should send GET request on url:
  
       http://localhost/task/state.php
       
and we will get output of  balance together with last update/last transaction that's been made. Example:

        Your current balance is: 3003.36
        Last updated amount is: -51321.64
        
## Unit tests

  For unit tests I've used <a href="https://phpunit.de/">PHPUnit</a> framework. There are tests for both GET and POST requests . I've installed <a href="https://github.com/rmccue/Requests">Requests</a> library to implement some of its methods in code for this tests. To try these tests run:
  
       composer test
    
  Running "composer test" should return OK status with 4 tests and 6 assertions.
  
## Conclusion

  This is my solution for given tasks and doing this I used : PHP, RabbitMq and MySQL. In case I forgot to describe something important  here code is commented aswell so I hope you can find it here then.

       
        
