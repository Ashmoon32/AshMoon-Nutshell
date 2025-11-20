<?php
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $content = $conn->real_escape_string($_POST['content']);
    $imagePath = NULL;

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $fileName = basename($_FILES["image"]["name"]);
        // Generate unique name to prevent overwriting: time_filename
        $target_file = $target_dir . time() . "_" . $fileName;
        
        // Move the file from temporary storage to our folder
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $imagePath = $target_file;
        }
    }

    if (!empty($content) || $imagePath) {
        $sql = "INSERT INTO posts (user_id, content, image) VALUES ('$user_id', '$content', '$imagePath')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        header("Location: index.php"); // Don't post if empty
    }
}
?>