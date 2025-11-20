<?php
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['user_id'];

// Get the ID of the user we are viewing (from URL like profile.php?id=5)
// If no ID provided, show my own profile
$view_user_id = isset($_GET['id']) ? $_GET['id'] : $my_id;

// Fetch User Info
$user_sql = "SELECT * FROM users WHERE id = $view_user_id";
$user_res = $conn->query($user_sql);
$user_data = $user_res->fetch_assoc();

if (!$user_data) { echo "User not found."; exit(); }

// Check Friendship Status
$is_friend = false;
$friend_check = $conn->query("SELECT * FROM friends WHERE (user_one=$my_id AND user_two=$view_user_id) OR (user_one=$view_user_id AND user_two=$my_id)");
if ($friend_check->num_rows > 0) {
    $is_friend = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $user_data['first_name']; ?>'s Profile</title>
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
<body class="bg-[#f0f2f5]">

    <!-- Navbar (Same as index) -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm px-4 py-2 flex justify-between items-center">
        <a href="index.php" class="text-blue-600 text-2xl font-bold">SocialMini</a>
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-600 hover:bg-gray-100 p-2 rounded-full"><i class="fa-solid fa-house"></i></a>
            <!-- <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-xl">
            <i class="fa-solid fa-moon dark:hidden"></i>
            <i class="fa-solid fa-sun hidden dark:block text-white"></i>
            </button> -->
            <?php
// Fetch Unread Notifications
$notif_q = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_to = $my_id AND seen = 0");
$notif_count = $notif_q->fetch_assoc()['count'];
?>

<!-- In Navbar HTML -->
<div class="relative group cursor-pointer mr-4">
    <i class="fa-solid fa-bell text-xl text-gray-600 dark:text-gray-300"></i>
    <?php if($notif_count > 0): ?>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?php echo $notif_count; ?></span>
    <?php endif; ?>
    
    <!-- Dropdown -->
    <div class="absolute right-0 mt-2 w-64 bg-white dark:bg-fbCard shadow-xl rounded-lg hidden group-hover:block overflow-hidden border dark:border-gray-700">
        <?php
        $n_sql = "SELECT notifications.*, users.first_name, users.avatar FROM notifications JOIN users ON notifications.user_from = users.id WHERE user_to = $my_id ORDER BY created_at DESC LIMIT 5";
        $n_res = $conn->query($n_sql);
        
        if($n_res->num_rows > 0) {
            while($notif = $n_res->fetch_assoc()) {
                $msg = ($notif['type'] == 'like') ? "liked your post." : "commented on your post.";
                echo '
                <a href="index.php" class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-fbInput flex items-center gap-3">
                    <img src="'.$notif['avatar'].'" class="w-8 h-8 rounded-full">
                    <div class="text-sm">
                        <span class="font-bold">'.$notif['first_name'].'</span> '.$msg.'
                    </div>
                </a>';
            }
            // Mark as seen when dropdown opens (Simplified logic: usually requires AJAX)
            $conn->query("UPDATE notifications SET seen=1 WHERE user_to=$my_id");
        } else {
            echo '<div class="p-4 text-center text-gray-500 text-sm">No notifications</div>';
        }
        ?>
    </div>
</div>
            <a href="logout.php" class="text-red-500 text-sm hover:underline">Logout</a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto pt-6">
        
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
            <div class="h-32 bg-gradient-to-r from-blue-400 to-purple-500"></div>
            <div class="px-8 pb-6 relative">
                <div class="absolute -top-12 left-8 w-24 h-24 group">
    <div class="border-4 border-white rounded-full overflow-hidden w-full h-full bg-white relative">
        <img src="<?php echo $user_data['avatar']; ?>" class="w-full h-full object-cover">
        
        <!-- Only show upload button if it's MY profile -->
        <?php if ($my_id == $view_user_id): ?>
        <form action="upload_avatar.php" method="POST" enctype="multipart/form-data" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
            <label class="cursor-pointer text-white text-xs text-center">
                <i class="fa-solid fa-camera text-xl mb-1"></i><br>Change
                <input type="file" name="avatar" class="hidden" onchange="this.form.submit()">
            </label>
        </form>
        <?php endif; ?>
    </div>
</div>
                <div class="pl-32 pt-2 flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl font-bold"><?php echo $user_data['first_name'] . " " . $user_data['last_name']; ?></h1>
                        <p class="text-gray-500">Joined: <?php echo date('M Y', strtotime($user_data['created_at'] ?? 'now')); ?></p>
                    </div>
                    
                    <!-- Friend Button (Don't show if viewing myself) -->
                    <?php if ($my_id != $view_user_id): ?>
                        <form action="friend_action.php" method="POST">
                            <input type="hidden" name="friend_id" value="<?php echo $view_user_id; ?>">
                            <?php if ($is_friend): ?>
                                <button type="submit" class="bg-gray-200 text-black px-4 py-2 rounded-md font-semibold hover:bg-gray-300">
                                    <i class="fa-solid fa-check"></i> Friends
                                </button>
                            <?php else: ?>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-700">
                                    <i class="fa-solid fa-user-plus"></i> Add Friend
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                    <a href="chat.php?user_id=<?php echo $view_user_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 ml-2">
    <i class="fa-regular fa-comment-dots"></i> Message
</a>
                </div>
            </div>
        </div>

        <!-- User's Posts -->
        <h3 class="text-xl font-bold text-gray-600 mb-4 px-2">Posts</h3>
        
        <?php
        // Fetch ONLY this user's posts
        $sql = "SELECT posts.*, users.first_name, users.last_name, users.avatar 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE users.id = $view_user_id
                ORDER BY posts.created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '
                <div class="bg-white rounded-lg shadow mb-4 p-4">
                    <div class="flex gap-3 mb-3">
                        <img src="'.$row['avatar'].'" class="w-10 h-10 rounded-full border">
                        <div>
                            <h4 class="font-semibold text-sm">'.$row['first_name'].' '.$row['last_name'].'</h4>
                            <span class="text-xs text-gray-500">'.$row['created_at'].'</span>
                        </div>
                    </div>
                    <p class="text-gray-800">'.$row['content'].'</p>
                </div>';
            }
        } else {
            echo "<div class='bg-white p-8 rounded shadow text-center text-gray-500'>No posts found.</div>";
        }
        ?>

    </div>

    <script src="main.js"></script>
</body>
</html>