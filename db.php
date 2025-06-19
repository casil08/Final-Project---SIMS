<?php
function db_connect() {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'sims - casil';

    $mysqli = new mysqli($host, $username, $password, $database);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
?>
