<?php
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$my_avatar = $_SESSION['avatar'];
$my_name = $_SESSION['first_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feed - AshMoon Nutshell</title>
    <!-- FIXED: Tailwind CSS Link -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <script src="main.js"></script> -->
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

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm px-4 py-2 flex justify-between items-center bg-white">
        <h1 class="text-blue-600 text-2xl font-bold">AshMoon Nutshell</h1>
        <!-- Add inside <nav> -->
<form action="search.php" method="GET" class="hidden md:block w-1/3">
    <div class="relative">
        <input type="text" name="q" placeholder="Search users..." class="w-full bg-gray-100 dark:bg-fbInput dark:text-white rounded-full px-4 py-2 pl-10 focus:outline-none">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
    </div>
</form>
        <div class="flex items-center gap-4">
            <a href="profile.php" class="flex items-center gap-2">
            <span class="font-semibold"><?php echo $my_name; ?></span>
            <img src="<?php echo $my_avatar; ?>" class="w-10 h-10 rounded-full border border-gray-300">
            </a>
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

    <div class="max-w-xl mx-auto pt-6 pb-10">
        
        <!-- Create Post -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <form action="post_action.php" method="POST" enctype="multipart/form-data">
                <div class="flex gap-4 border-b pb-4">
                    <img src="<?php echo $my_avatar; ?>" class="w-10 h-10 rounded-full object-cover">
                    <input type="text" name="content" placeholder="What's on your mind?" class="flex-1 bg-gray-100 rounded-full px-4 focus:outline-none transition hover:bg-gray-200" required>
                </div>
                <div class="pl-14 pb-2">
                    <label class="cursor-pointer text-green-600 hover:bg-green-50 px-2 py-1 rounded text-sm font-semibold">
                        <i class="fa-solid fa-image"></i> Add Photo
                        <input type="file" name="image" class="hidden" accept="image/*">
                    </label>
                </div>
                <div class="flex justify-end mt-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-1 rounded-md font-semibold hover:bg-blue-700">Post</button>
                </div>
            </form>
        </div>
        <script>
    const toggleBtn = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    // Check saved preference
//     if(localStorage.getItem('theme') === 'dark') {
//         html.classList.add('dark');
//     }

//     toggleBtn.addEventListener('click', () => {
//         if(html.classList.contains('dark')) {
//             html.classList.remove('dark');
//             localStorage.setItem('theme', 'light');
//         } else {
//             html.classList.add('dark');
//             localStorage.setItem('theme', 'dark');
//         }
//     });
// </script>
</body>
</html>

        <!-- Feed Loop -->
        <?php
        $sql = "SELECT posts.*, users.first_name, users.last_name, users.avatar 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $post_id = $row['id'];
                
                // 1. Count Likes
                $like_q = $conn->query("SELECT * FROM likes WHERE post_id = $post_id");
                $like_count = $like_q->num_rows;

                // 2. Check if I liked it
                $i_liked = $conn->query("SELECT * FROM likes WHERE post_id = $post_id AND user_id = $my_id");
                $like_color = ($i_liked->num_rows > 0) ? "text-blue-600 font-bold" : "text-gray-500";

                // 3. Get Comments
                $comments_q = $conn->query("SELECT comments.*, users.first_name, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $post_id ORDER BY created_at ASC");

                echo '
                <div class="bg-white rounded-lg shadow mb-4">
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3">
                                <img src="'.$row['avatar'].'" class="w-10 h-10 rounded-full border">
                                <div>
                                    <h4 class="font-semibold text-sm">               
                                    <a href="profile.php?id='.$row['user_id'].'" class="hover:underline text-blue-900"> '.$row['first_name'].' '.$row['last_name'].'
                                    </a>
                                    </h4>
                                    <span class="text-xs text-gray-500">'.$row['created_at'].'</span>
                                </div>
                            </div>';
                            
                            // Delete Button (Only if I own the post)
                            // if ($row['user_id'] == $my_id) {
                            //     echo '<a href="delete_action.php?id='.$post_id.'" class="text-gray-400 hover:text-red-500" onclick="return confirm(\'Delete this post?\')"><i class="fa-solid fa-trash"></i></a>';
                            // }
                            // Edit Button
                            if ($row['user_id'] == $my_id) {
                            echo '<div class="flex gap-2">
                            <a href="edit_post.php?id='.$post_id.'" class="text-gray-400 hover:text-blue-500"><i class="fa-solid fa-pen"></i></a>
                            <a href="delete_action.php?id='.$post_id.'" class="text-gray-400 hover:text-red-500" onclick="return confirm(\'Delete?\')"><i class="fa-solid fa-trash"></i></a>
                            </div>';
                           
}
               
            echo '  </div>
                        <p class="text-gray-800 mt-3">'.$row['content'].'</p>';

                // --- HERE IS THE FIX --- 
                // We broke the echo above with ';', now we check for image
                if ($row['image']) {
                    echo '<img src="'.$row['image'].'" class="mt-3 rounded-lg w-full object-cover max-h-96 border border-gray-100">';
                }
                // Now we start echo again for the rest of the card
                
                echo '</div> 
                    <!-- End of p-4 content container, now Stats Bar -->

                    <!-- Like / Stats -->
                    <div class="px-4 py-2 border-t flex justify-between items-center text-sm text-gray-500">
                        <span><i class="fa-solid fa-thumbs-up text-blue-500"></i> '.$like_count.'</span>
                        <span>'.$comments_q->num_rows.' comments</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t border-b flex">
                        <form action="like_action.php" method="POST" class="flex-1">
                            <input type="hidden" name="post_id" value="'.$post_id.'">
                            <button type="submit" class="w-full py-2 hover:bg-gray-100 '.$like_color.'">
                                <i class="fa-regular fa-thumbs-up"></i> Like
                            </button>
                        </form>
                        <button class="flex-1 py-2 hover:bg-gray-100 text-gray-500">
                            <i class="fa-regular fa-message"></i> Comment
                        </button>
                    </div>

                    <!-- Comments Section -->
                    <div class="p-4 bg-gray-50">
                        <div class="space-y-2 mb-3">';
                        
                        while($com = $comments_q->fetch_assoc()) {
                            echo '
                            <div class="flex gap-2">
                                <img src="'.$com['avatar'].'" class="w-8 h-8 rounded-full">
                                <div class="bg-gray-200 rounded-2xl px-3 py-2">
                                    <span class="font-bold text-xs block">'.$com['first_name'].'</span>
                                    <span class="text-sm">'.$com['comment'].'</span>
                                </div>
                            </div>';
                        }

                echo '  </div>
                        <form action="comment_action.php" method="POST" class="flex gap-2">
                            <input type="hidden" name="post_id" value="'.$post_id.'">
                            <img src="'.$my_avatar.'" class="w-8 h-8 rounded-full">
                            <input type="text" name="comment" placeholder="Write a comment..." class="flex-1 bg-white border rounded-full px-3 text-sm focus:outline-none" required>
                            <button type="submit" class="text-blue-500"><i class="fa-solid fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>';
            }
        }
