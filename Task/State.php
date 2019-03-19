<?php


//route for reading state from database

//connect to database

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
// echo "Connected successfully";


    /*  making GET request for current balance and last
       transaction that have been made */

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $sql = "SELECT * FROM account";
        $res = mysqli_query($conn, $sql);

        while ($print = mysqli_fetch_array($res)) {
            echo "Your current balance is: " . $print['balance'] . PHP_EOL;
            echo "Last updated amount is: " . $print['updated_at'] . PHP_EOL;
        };

    }

