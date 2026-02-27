<?php
$host = "77.37.35.182";
$user = "u812122863_neama";
$pass = "?PuzuDXOo";
$db = "u812122863_lakum_artspace";

// Object-oriented connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $conn->connect_error
    ]));
}
?>