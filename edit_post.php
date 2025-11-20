<?php
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) header("Location: login.php");

// Get Post Data
$post_id = $_GET['id'];
$sql = "SELECT * FROM posts WHERE id = $post_id AND user_id = " . $_SESSION['user_id'];
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Post not found or permission denied.";
    exit();
}

$post = $result->fetch_assoc();

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $conn->real_escape_string($_POST['content']);
    $update_sql = "UPDATE posts SET content = '$content' WHERE id = $post_id";
    if ($conn->query($update_sql)) {
        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-xl w-96">
        <h2 class="text-xl font-bold mb-4">Edit Post</h2>
        <form method="POST">
            <textarea name="content" class="w-full border p-3 rounded-md mb-4 h-32"><?php echo $post['content']; ?></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Save</button>
                <a href="index.php" class="flex-1 bg-gray-300 text-center py-2 rounded-md hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>