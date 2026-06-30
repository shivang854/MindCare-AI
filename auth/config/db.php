
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "mindcare_ai"
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>