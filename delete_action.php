<?php
require 'includes/db_connect.php';

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Only delete if the post belongs to the current user
    $sql = "DELETE FROM posts WHERE id=$post_id AND user_id=$user_id";
    $conn->query($sql);
}
header("Location: index.php");
?>