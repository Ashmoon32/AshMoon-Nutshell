<?php
require 'includes/db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$my_id = $_SESSION['user_id'];
$chat_partner_id = $_GET['user_id'];

// Get Partner Info
$user_sql = "SELECT * FROM users WHERE id = $chat_partner_id";
$partner = $conn->query($user_sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat with <?php echo $partner['first_name']; ?></title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class', // This enables manual dark mode toggling
        theme: {
            extend: {
                colors: {
                    // Facebook Dark Mode Colors
                    fbDark: '#18191a',
                    fbCard: '#242526',
                    fbInput: '#3a3b3c',
                    fbText: '#e4e6eb',
                    fbLight: '#f0f2f5'
                }
            }
        }
    }
</script>
<style>
    /* Smooth theme transition */
    body, div, nav, input, textarea { transition: background-color 0.3s, color 0.3s; }
</style>
</head>
<body class="bg-gray-100 h-screen flex flex-col">

    <!-- Header -->
    <div class="bg-white shadow p-4 flex items-center gap-4 sticky top-0 z-10">
        <a href="index.php" class="text-gray-500 hover:bg-gray-100 p-2 rounded-full"><i class="fa-solid fa-arrow-left"></i></a>
        <img src="<?php echo $partner['avatar']; ?>" class="w-10 h-10 rounded-full object-cover">
        <h1 class="font-bold text-lg"><?php echo $partner['first_name'] . ' ' . $partner['last_name']; ?></h1>
        <!-- Add inside <nav> -->
<form action="search.php" method="GET" class="hidden md:block w-1/3">
    <div class="relative">
        <input type="text" name="q" placeholder="Search users..." class="w-full bg-gray-100 dark:bg-fbInput dark:text-white rounded-full px-4 py-2 pl-10 focus:outline-none">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
    </div>
</form>
    </div>

    <!-- Chat Area -->
    <div id="chat-box" class="flex-1 overflow-y-auto p-4 space-y-2 pb-20">
        <!-- Messages load here via JS -->
    </div>

    <!-- Input Area -->
    <div class="bg-white p-4 border-t fixed bottom-0 w-full">
        <form id="chat-form" class="flex gap-2 max-w-4xl mx-auto">
            <input type="hidden" id="receiver_id" value="<?php echo $chat_partner_id; ?>">
            <input type="text" id="message-input" class="flex-1 border p-2 rounded-full focus:outline-none px-4" placeholder="Type a message..." autocomplete="off">
            <button type="submit" class="bg-blue-600 text-white px-6 rounded-full hover:bg-blue-700"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const receiverId = document.getElementById('receiver_id').value;
        let shouldScroll = true;

        // 1. Function to Load Messages
        function loadMessages() {
            fetch('get_messages.php?user_id=' + receiverId)
                .then(response => response.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    if(shouldScroll) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                        shouldScroll = false; // Only scroll to bottom on first load or new message
                    }
                });
        }

        // 2. Load initially and then every 1 second (Polling)
        loadMessages();
        setInterval(loadMessages, 1000);

        // 3. Send Message Logic
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('message-input');
            const message = input.value;
            if (!message.trim()) return;

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                input.value = ''; // Clear input
                shouldScroll = true; // Force scroll to bottom
                loadMessages(); // Refresh immediately
            });
        });
    </script>
</body>
</html>