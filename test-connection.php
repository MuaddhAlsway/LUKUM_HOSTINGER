<?php
$host = "77.37.35.182";
$user = "u812122863_neama";
$pass = "?PuzuDXOo";
$db = "u812122863_lakum_artspace";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo "ERROR: " . mysqli_connect_error();
} else {
    echo "SUCCESS: Connected to Hostinger database";
    mysqli_close($conn);
}
?>


