<?php
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) header("Location: login.php");
$my_id = $_SESSION['user_id'];

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { fbDark: '#18191a', fbCard: '#242526', fbInput: '#3a3b3c', fbText: '#e4e6eb', fbLight: '#f0f2f5' } } } }
    </script>
</head>
<body class="bg-fbLight dark:bg-fbDark dark:text-fbText">

    <!-- Reuse your Navbar here -->
    <nav class="bg-white dark:bg-fbCard shadow-sm px-4 py-2 mb-4 flex justify-between">
        <a href="index.php" class="text-blue-600 text-2xl font-bold">SocialMini</a>
        <a href="index.php" class="text-gray-500">Back to Feed</a>
    </nav>

    <div class="max-w-2xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

        <?php
        if ($query != '') {
            $sql = "SELECT * FROM users WHERE (first_name LIKE '%$query%' OR last_name LIKE '%$query%') AND id != $my_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Check friendship status
                    $f_id = $row['id'];
                    $check = $conn->query("SELECT * FROM friends WHERE (user_one=$my_id AND user_two=$f_id) OR (user_one=$f_id AND user_two=$my_id)");
                    $is_friend = ($check->num_rows > 0);

                    echo '
                    <div class="bg-white dark:bg-fbCard p-4 rounded-lg shadow mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="'.$row['avatar'].'" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <a href="profile.php?id='.$row['id'].'" class="font-bold text-lg hover:underline">
                                    '.$row['first_name'].' '.$row['last_name'].'
                                </a>
                                <p class="text-sm text-gray-500">User</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="profile.php?id='.$row['id'].'" class="bg-blue-100 text-blue-600 px-3 py-1 rounded font-semibold text-sm">View Profile</a>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo "<p class='text-gray-500'>No users found.</p>";
            }
        }
        ?>
    </div>
    
    <!-- Script for Dark Mode logic (copy from index.php) -->
    <script>
        if(localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>
</body>
</html>