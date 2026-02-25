<?php
$hostname = 'localhost';
$dbName = 'events_web';
$username = 'admin1';
$password = 'abc123';
$conn = new mysqli($hostname, $username, $password, $dbName);

function getConnection(): mysqli
{
    global $conn;
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

