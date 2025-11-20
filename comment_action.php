<?php
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment = $conn->real_escape_string($_POST['comment']);

    $sql = "INSERT INTO comments (user_id, post_id, comment) VALUES ($user_id, $post_id, '$comment')";
    $conn->query($sql);
}
header("Location: index.php");
?>