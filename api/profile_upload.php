<?php
session_start();

include '../auth/config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

Configuration::instance([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
        'api_key' => getenv('CLOUDINARY_API_KEY'),
        'api_secret' => getenv('CLOUDINARY_API_SECRET'),
    ],
    'url' => [
        'secure' => true
    ]
]);


$cloudinary = new Cloudinary(
    Configuration::instance()
);

if (isset($_POST['upload'])) {

    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {

        $user_id = $_SESSION['user_id'];

        $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            die("Only JPG, JPEG, PNG and WEBP allowed.");
        }

        try {

            $upload = $cloudinary->uploadApi()->upload(
                $_FILES['profile']['tmp_name'],
                [
                    'folder' => 'mindcare_profiles',
                    'public_id' => 'user_' . $user_id,
                    'overwrite' => true
                ]
            );

            $imageUrl = $upload['secure_url'];

            $stmt = $conn->prepare("
                UPDATE users
                SET profile_image=?
                WHERE id=?
            ");

            $stmt->bind_param("si", $imageUrl, $user_id);
            $stmt->execute();

            header("Location: ../profile.php?upload=success");
            exit();

        } catch (Exception $e) {

            die("Cloudinary Error: " . $e->getMessage());

        }

    }

}
