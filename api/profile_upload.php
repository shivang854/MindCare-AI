<?php

session_start();


include '../auth/config/db.php';


if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

if(isset($_POST['upload'])){

    if(isset($_FILES['profile']) && $_FILES['profile']['error']==0){

        $user_id=$_SESSION['user_id'];

        $ext=strtolower(pathinfo($_FILES['profile']['name'],PATHINFO_EXTENSION));

        $allowed=['jpg','jpeg','png','webp'];

        if(!in_array($ext,$allowed)){
            die("Only JPG, JPEG, PNG and WEBP allowed.");
        }

        $filename="user_".$user_id."_".time().".".$ext;

        $target="../assets/uploads/profiles/".$filename;

        if(move_uploaded_file($_FILES['profile']['tmp_name'],$target)){

            $stmt=$conn->prepare("
            UPDATE users
            SET profile_image=?
            WHERE id=?
            ");

            $stmt->bind_param("si",$filename,$user_id);
            $stmt->execute();

            echo "Redirecting...";

            header("Location: /MindCare-AI/profile.php?upload=success");

    
            exit();

        }else{

            echo "Upload failed.";

        }

    }

}
?>