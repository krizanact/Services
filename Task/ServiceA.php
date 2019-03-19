<?php


require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;



// Receive json data if POST request is received
 
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    /**
     * @param $data : decoded json data
     * @param $amount : decimal represeting value that has been sent
     * @param $currency : string representing currency EUR
     */
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'];
    $currency = $data['currency'];
    // if request fills all statements below, message will be sent to Service B
    if (is_numeric($amount) && $amount > -100000000 && $amount < 100000000 && $currency == 'EUR') {


            //send this message in json format to ServiceB using RabbitMQ below

            $jsonMEssage = json_encode([
                'amount' => $amount * 100,     // convert  Euros in Cents so message will show amount in Cents instead of Euros
                'currency' => $currency,
            ], JSON_PRETTY_PRINT);


        //connect to RabbitMQ
        
        $host = "moose.rmq.cloudamqp.com";
        $port = "5672";
        $user = "hzamycrw";
        $password = "vLSC__bFVwtFHW-0p2avNvasqB9j6Kj0";
        $vhost = "hzamycrw";
        $exchange = 'router';
        $queue = 'msgs';

        $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $channel = $connection->channel();
        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
        */
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */

        $channel->queue_declare($queue, false, true, false, false);
        /*
                        name: $exchange
                        type: direct
                        passive: false
                        durable: true // the exchange will survive server restarts
                        auto_delete: false //the exchange won't be deleted once the channel is closed.
                    */
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange);
         //send json data(message) to serviceB
        $message = new AMQPMessage($jsonMEssage, array(
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ));
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $connection->close();


        echo $jsonMEssage .PHP_EOL . "Message sent to Service B!";

}
    else
        {
            //if one of IF statements above isn't filled message won't be sent and we'll get 400 response code
            http_response_code(400);
            echo "Invalid Request!";
        }
}




