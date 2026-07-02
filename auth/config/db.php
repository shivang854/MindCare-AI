<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$envPath = __DIR__ . '/../../';

if (file_exists($envPath . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->safeLoad();
}

$conn = mysqli_connect(
    $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
    $_ENV['DB_USER'] ?? getenv('DB_USER'),
    $_ENV['DB_PASS'] ?? getenv('DB_PASS'),
    $_ENV['DB_NAME'] ?? getenv('DB_NAME'),
    (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT'))
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
