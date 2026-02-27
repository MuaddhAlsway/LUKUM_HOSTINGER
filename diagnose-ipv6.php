<?php
echo "<h2>IPv6 Diagnosis</h2>";
echo "<p>Server IP: " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p>Your IP: " . $_SERVER['REMOTE_ADDR'] . "</p>";

$host = "77.37.35.182";
$user = "u812122863_neama";
$pass = "?PuzuDXOo";
$db = "u812122863_lakum_artspace";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    $error = mysqli_connect_error();
    echo "<p style='color:red;'><strong>Connection Error:</strong></p>";
    echo "<p>" . $error . "</p>";
    
    if (strpos($error, '2a02:') !== false || strpos($error, '::') !== false) {
        echo "<p style='color:red;'><strong>ISSUE: IPv6 is being used!</strong></p>";
        echo "<p>Hostinger's MySQL rejects IPv6 connections.</p>";
        echo "<p><strong>Action:</strong> Contact Hostinger support to enable IPv6 for MySQL.</p>";
    }
} else {
    echo "<p style='color:green;'><strong>Connected successfully!</strong></p>";
    mysqli_close($conn);
}
?>
