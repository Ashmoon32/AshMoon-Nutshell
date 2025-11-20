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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f0f2f5]">

    <!-- Navbar (Same as index) -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm px-4 py-2 flex justify-between items-center">
        <a href="index.php" class="text-blue-600 text-2xl font-bold">SocialMini</a>
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-600 hover:bg-gray-100 p-2 rounded-full"><i class="fa-solid fa-house"></i></a>
            <a href="logout.php" class="text-red-500 text-sm hover:underline">Logout</a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto pt-6">
        
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
            <div class="h-32 bg-gradient-to-r from-blue-400 to-purple-500"></div>
            <div class="px-8 pb-6 relative">
                <div class="absolute -top-12 left-8 border-4 border-white rounded-full overflow-hidden w-24 h-24 bg-white">
                    <img src="<?php echo $user_data['avatar']; ?>" class="w-full h-full object-cover">
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
</body>
</html>