<?php
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    // Generate random avatar
    $avatar = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . $fname;

    $sql = "INSERT INTO users (first_name, last_name, email, password, avatar) 
            VALUES ('$fname', '$lname', '$email', '$pass', '$avatar')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - SocialMini</title>
    <!-- CORRECTED SCRIPT TAG -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-xl w-96">
        <h1 class="text-green-600 text-3xl font-bold mb-6 text-center">Sign Up</h1>
        
        <?php if(isset($error)) echo "<p class='text-red-500 mb-2'>$error</p>"; ?>

        <form method="POST" action="register.php" class="flex flex-col gap-4">
            <div class="flex gap-2">
                <input type="text" name="fname" placeholder="First Name" class="border p-3 rounded-md w-1/2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                <input type="text" name="lname" placeholder="Last Name" class="border p-3 rounded-md w-1/2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>
            <input type="email" name="email" placeholder="Email" class="border p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <input type="password" name="password" placeholder="New Password" class="border p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <button type="submit" class="bg-green-600 text-white font-bold py-3 rounded-md hover:bg-green-700">Sign Up</button>
        </form>
        <a href="login.php" class="block text-center mt-4 text-blue-500 hover:underline">Back to Login</a>
    </div>
</body>
</html>