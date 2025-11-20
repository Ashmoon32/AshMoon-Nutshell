<?php
require 'includes/db_connect.php';

// Handle Login Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['avatar'] = $row['avatar'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SocialMini</title>
    <!-- CORRECTED SCRIPT TAG -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-xl w-96">
        <h1 class="text-blue-600 text-3xl font-bold mb-6 text-center">SocialMini</h1>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-center mb-4 bg-red-100 p-2 rounded'>$error</p>"; ?>

        <form method="POST" action="login.php" class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="Email" class="border p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="password" name="password" placeholder="Password" class="border p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <button type="submit" class="bg-blue-600 text-white font-bold py-3 rounded-md hover:bg-blue-700 transition">Log In</button>
        </form>
        
        <div class="mt-4 text-center border-t pt-4">
            <p class="text-gray-600 text-sm">Don't have an account?</p>
            <a href="register.php" class="text-green-600 font-bold hover:underline">Create New Account</a>
        </div>
    </div>

</body>
</html>