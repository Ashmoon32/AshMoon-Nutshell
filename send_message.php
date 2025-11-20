<?php
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender = $_SESSION['user_id'];
    $receiver = $_POST['receiver_id'];
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($sender, $receiver, '$message')";
        $conn->query($sql);
    }
}
?>