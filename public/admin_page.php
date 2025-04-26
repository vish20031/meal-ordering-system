<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: index.html');
    exit();
}

// Load JSON Data
$users = json_decode(file_get_contents('../data/users.json'), true);
$orders = json_decode(file_get_contents('../data/orders.json'), true);
$messages = json_decode(file_get_contents('../data/messages.json'), true);

// Handle account creation and deletion
if (isset($_POST['create_user'])) {
    $newUser = [
        'username' => $_POST['new_username'],
        'password' => $_POST['new_password'],
        'role' => 'user'
    ];
    $users[] = $newUser;
    file_put_contents('../data/users.json', json_encode($users, JSON_PRETTY_PRINT));
    header('Location: admin_page.php');
}

if (isset($_POST['delete_user'])) {
    $deleteUsername = $_POST['delete_username'];
    $users = array_filter($users, fn($u) => $u['username'] != $deleteUsername);
    file_put_contents('../data/users.json', json_encode(array_values($users), JSON_PRETTY_PRINT));
    header('Location: admin_page.php');
}

// Handle order approval
if (isset($_POST['approve_order'])) {
    $orderId = $_POST['order_id'];
    foreach ($orders as &$order) {
        if ($order['id'] == $orderId) {
            $order['status'] = 'Approved';
        }
    }
    file_put_contents('../data/orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    header('Location: admin_page.php');
}

if (isset($_POST['reject_order'])) {
    $orderId = $_POST['order_id'];
    foreach ($orders as &$order) {
        if ($order['id'] == $orderId) {
            $order['status'] = 'Rejected';
        }
    }
    file_put_contents('../data/orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    header('Location: admin_page.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-semibold text-blue-700">Admin Dashboard</h2>
        <nav class="space-x-4">
            <a href="#create_user" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create User</a>
            <a href="#delete_user" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Delete User</a>
            <a href="../logout.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Logout</a>
        </nav>
    </div>

    <div id="create_user" class="mb-8 p-6 bg-white rounded-lg shadow-md hidden">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Create User</h3>
        <form method="post" class="flex flex-col space-y-4">
            <div>
                <label for="new_username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" name="new_username" id="new_username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" name="new_password" id="new_password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" name="create_user" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create User</button>
        </form>
    </div>

    <div id="delete_user" class="mb-8 p-6 bg-white rounded-lg shadow-md hidden">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Delete User</h3>
        <form method="post" class="flex flex-col space-y-4">
            <div>
                <label for="delete_username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" name="delete_username" id="delete_username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" name="delete_user" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Delete User</button>
        </form>
    </div>

    <div class="mb-8">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Orders to Approve</h3>
        <?php foreach ($orders as $order): ?>
            <div class="p-4 bg-white rounded-lg shadow-md mb-4">
                <p class="text-gray-700">Order ID: <span class="font-semibold"><?= $order['id'] ?></span></p>
                <p class="text-gray-700">User: <span class="font-semibold"><?= $order['username'] ?></span></p>
                <p class="text-gray-700">Visitors: <span class="font-semibold"><?= $order['visitors'] ?></span></p>
                <p class="text-gray-700">Meal Type: <span class="font-semibold"><?= $order['meal_type'] ?></span></p>
                <p class="text-gray-700">Status: <span class="font-semibold"><?= $order['status'] ?></span></p>
                <div class="mt-4 flex space-x-2">
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="approve_order" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Approve</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="reject_order" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Reject</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="../logout.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Logout</a>
    <script>
        document.querySelectorAll('nav a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    // Hide all sections
                    document.querySelectorAll('.mb-8').forEach(section => {
                        section.classList.add('hidden');
                    });
                    // Show the target section
                    targetElement.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
