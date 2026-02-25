<?php
$hostname = 'localhost';
$dbName = 'event_web';
$username = 'tester';
$password = '123abc';
$conn = new mysqli($hostname, $username, $password, $dbName);

function getConnection(): mysqli
{
    global $conn;
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

