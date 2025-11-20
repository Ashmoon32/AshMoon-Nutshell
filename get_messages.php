<?php
require 'includes/db_connect.php';

$me = $_SESSION['user_id'];
$them = $_GET['user_id'];

$sql = "SELECT * FROM messages 
        WHERE (sender_id = $me AND receiver_id = $them) 
           OR (sender_id = $them AND receiver_id = $me) 
        ORDER BY created_at ASC";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $is_me = ($row['sender_id'] == $me);
    // Different styles for Me vs Them
    $align = $is_me ? 'text-right' : 'text-left';
    $bg = $is_me ? 'bg-blue-500 text-white rounded-l-lg rounded-tr-lg' : 'bg-gray-200 text-gray-800 rounded-r-lg rounded-tl-lg';
    $flex = $is_me ? 'justify-end' : 'justify-start';

    echo '
    <div class="flex '.$flex.' mb-2">
        <div class="px-4 py-2 max-w-[70%] '.$bg.' text-sm shadow-sm">
            '.$row['message'].'
        </div>
    </div>
    ';
}
?>