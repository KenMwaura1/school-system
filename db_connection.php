<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "students";

try {
    $db = new mysqli($servername, $username, $password, $dbname);
    if ($db->connect_error) {
        throw new Exception("Connection failed: " . $db->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>