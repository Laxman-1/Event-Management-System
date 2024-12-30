
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('background.jpg');">
    <div class="bg-white bg-opacity-80 p-8 rounded-lg shadow-lg w-full max-w-md animate-fadeIn">
        <form action="backend/login_check.php" method="POST">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">User Login</h2>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="psw" class="block text-gray-700">Password</label>
                <input type="password" id="psw" name="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <input type="submit" value="Login" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition duration-300">
            <div class="mt-4 text-right">
                <a href="User_register.php" class="text-blue-500 hover:underline">Register</a>
            </div>
        </form>
    </div>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 1s ease-in-out;
        }
    </style>
</body>
</html>