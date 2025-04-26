<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: index.html');
    exit();
}

// Load JSON
$menu = json_decode(file_get_contents('../data/menu.json'), true);
$orders = json_decode(file_get_contents('../data/orders.json'), true);

// Handle Order Placing
if (isset($_POST['place_order'])) {
    $selected_menu = null;
    foreach ($menu as $m) {
        if ($m['id'] == $_POST['menu_id']) {
            $selected_menu = $m;
        }
    }

    if ($selected_menu) {
        $visitors = $_POST['visitors'];
        $total_price = $visitors * $selected_menu['price'];

        $newOrder = [
            'id' => uniqid(),
            'username' => $_SESSION['username'],
            'request_date' => $_POST['request_date'],
            'visitors' => $visitors,
            'meal_type' => $selected_menu['meal_type'],
            'menu' => $selected_menu['items'],
            'price_per_person' => $selected_menu['price'],
            'total_price' => $total_price,
            'status' => 'Pending'
        ];
        $orders[] = $newOrder;
        file_put_contents('../data/orders.json', json_encode($orders, JSON_PRETTY_PRINT));
        header('Location: user_page.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <h2 class="text-3xl font-semibold text-blue-700 mb-8">User Dashboard</h2>

    <nav class="bg-white rounded-lg shadow-md p-4 mb-8">
        <ul class="flex space-x-6">
            <li><a href="user_page.php" class="text-blue-500 hover:text-blue-700 font-semibold">Place Order</a></li>
            <li><a href="view_past_orders.php" class="text-blue-500 hover:text-blue-700 font-semibold">View Past Orders</a></li>
            <li><a href="../logout.php" class="text-red-500 hover:text-red-700 font-semibold">Logout</a></li>
        </ul>
    </nav>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Place New Order</h3>
        <form method="post" class="space-y-4">
            <div>
                <label for="request_date" class="block text-gray-700 text-sm font-bold mb-2">Request Date:</label>
                <input type="date" name="request_date" id="request_date" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="visitors" class="block text-gray-700 text-sm font-bold mb-2">Number of Visitors:</label>
                <input type="number" name="visitors" id="visitors" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="menu_id" class="block text-gray-700 text-sm font-bold mb-2">Select Menu:</label>
                <select name="menu_id" id="menu_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($menu as $m): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= $m['meal_type'] ?> (<?= $m['items'] ?>) - Rs.<?= $m['price'] ?>/person
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="place_order"
                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Place Order
            </button>
        </form>
    </div>
</body>
</html>
