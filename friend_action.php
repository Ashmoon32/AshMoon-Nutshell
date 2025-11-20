<?php
require 'includes/db_connect.php';

if (isset($_POST['friend_id'])) {
    $me = $_SESSION['user_id'];
    $friend = $_POST['friend_id'];

    // Check if friendship exists
    $check = $conn->query("SELECT * FROM friends WHERE (user_one=$me AND user_two=$friend) OR (user_one=$friend AND user_two=$me)");

    if ($check->num_rows > 0) {
        // If exists, delete (Unfriend)
        $conn->query("DELETE FROM friends WHERE (user_one=$me AND user_two=$friend) OR (user_one=$friend AND user_two=$me)");
    } else {
        // If not, insert (Add Friend)
        $conn->query("INSERT INTO friends (user_one, user_two) VALUES ($me, $friend)");
    }
}

// Redirect back to the profile page we came from
header("Location: profile.php?id=" . $friend);
?>