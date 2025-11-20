<?php
require 'includes/db_connect.php';

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if already liked
    $check = $conn->query("SELECT * FROM likes WHERE post_id=$post_id AND user_id=$user_id");

    if ($check->num_rows > 0) {
        // Unlike
        $conn->query("DELETE FROM likes WHERE post_id=$post_id AND user_id=$user_id");
    } else {
        // Like
        $conn->query("INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
    }
}
// Notification Logic
if ($check->num_rows == 0) { // Only notify on LIKE, not unlike
    // Get post owner
    $p_row = $conn->query("SELECT user_id FROM posts WHERE id=$post_id")->fetch_assoc();
    $owner_id = $p_row['user_id'];
    
    if ($owner_id != $user_id) { // Don't notify if I like my own post
        $conn->query("INSERT INTO notifications (user_to, user_from, type, post_id) VALUES ($owner_id, $user_id, 'like', $post_id)");
    }
}
header("Location: index.php"); // Go back to feed
?>