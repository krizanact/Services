<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

    //connec to RabbitMQ

    $host = "moose.rmq.cloudamqp.com";
    $port = "5672";
    $user = "hzamycrw";
    $password = "vLSC__bFVwtFHW-0p2avNvasqB9j6Kj0";
    $vhost = "hzamycrw";
    $exchange = 'router';
    $queue = 'msgs';
    $consumerTag = 'consumer';

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
    /**
     * @param \PhpAmqpLib\Message\AMQPMessage $message
     */

    function receive(AMQPMessage $message)
    {
        echo "\n--------\n";
        echo $message->body;
        echo "\n--------\n";

        $body_ = json_decode($message->body, true);   // getting the data from the received message and converting it because output is json type

        $amount = $body_['amount'] / 100;   //converts Cents back  to Euro because message has been shown in Cents and database will be updated in Euros
        $currency = $body_['currency'];
        update_data($amount, $currency);   // make database update calling this function defined below
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    }

    /**
     * This function inserts data in database if json is valid.
     *
     * @param $amount : decimal represeting value that has been received
     * @param $currency : string representing currency EUR
     */
    function update_data($amount, $currency)
    {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $db = "task";

// Create connection
        $conn = new mysqli($servername, $username, $password, $db);

// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $x = 0;
        $sql = "SELECT balance FROM account";
        $res = mysqli_query($conn, $sql);

        while ($print = mysqli_fetch_array($res)) {
            $x = $print['balance'];                   //save last recorded balance from database in $x
        };

        $sum = $amount + $x;                          //update balance with new data

        $sql = "UPDATE account SET balance = ' $sum ' ,updated_at = '$amount ' WHERE id = 1";

        if ($conn->query($sql) === TRUE) {
            echo "Successfully Updated! ";

        }
    }


    /*
        queue: Queue from where to get the messages
        consumer_tag: Consumer identifier
        no_local: Don't receive messages published by this consumer.
        no_ack: Tells the server if the consumer will acknowledge the messages.
        exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
        nowait:
        callback: A PHP Callback
    */

    $channel->basic_consume($queue, $consumerTag, false, false, false, true, 'receive');
    /**
     * @param \PhpAmqpLib\Channel\AMQPChannel $channel
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     */
    function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }

    register_shutdown_function('shutdown', $channel, $connection);
    while (count($channel->callbacks)) {
        $channel->wait();
    }













