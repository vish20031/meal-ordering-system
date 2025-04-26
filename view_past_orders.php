<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: index.html');
    exit();
}

$orders = json_decode(file_get_contents('../data/orders.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <h2 class="text-3xl font-semibold text-blue-700 mb-8">Past Orders</h2>

    <nav class="bg-white rounded-lg shadow-md p-4 mb-8">
        <ul class="flex space-x-6">
            <li><a href="user_page.php" class="text-blue-500 hover:text-blue-700 font-semibold">Place Order</a></li>
            <li><a href="view_past_orders.php" class="text-blue-500 hover:text-blue-700 font-semibold">View Past Orders</a></li>
            <li><a href="../logout.php" class="text-red-500 hover:text-red-700 font-semibold">Logout</a></li>
        </ul>
    </nav>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <?php if ($order['username'] == $_SESSION['username']): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                    <p class="text-gray-700">Order ID: <span class="font-semibold"><?= $order['id'] ?></span></p>
                    <p class="text-gray-700">Visitors: <span class="font-semibold"><?= $order['visitors'] ?></span></p>
                    <p class="text-gray-700">Meal Type: <span class="font-semibold"><?= $order['meal_type'] ?></span></p>
                    <p class="text-gray-700">Total Price: <span class="font-semibold">Rs.<?= $order['total_price'] ?></span></p>
                    <p class="text-gray-700">Status: <span class="font-semibold"><?= $order['status'] ?></span></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500">No past orders found.</p>
    <?php endif; ?>
</body>
</html>
