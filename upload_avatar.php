<?php
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "uploads/";
    $fileName = time() . "_avatar_" . basename($_FILES["avatar"]["name"]);
    $target_file = $target_dir . $fileName;

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        // Update database
        $sql = "UPDATE users SET avatar = '$target_file' WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['avatar'] = $target_file; // Update session immediately
            header("Location: profile.php");
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>