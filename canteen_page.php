<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'canteen') {
    header('Location: index.html');
    exit();
}

// Load JSON
$menu = json_decode(file_get_contents('../data/menu.json'), true);
$orders = json_decode(file_get_contents('../data/orders.json'), true);

// Handle Menu Update
if (isset($_POST['add_menu'])) {
    $menuItem = [
        'id' => uniqid(),
        'meal_type' => $_POST['meal_type'],
        'items' => $_POST['items'],
        'price' => $_POST['price']
    ];
    $menu[] = $menuItem;
    file_put_contents('../data/menu.json', json_encode($menu, JSON_PRETTY_PRINT));
    header('Location: canteen_page.php');
}

// Handle Order Received
if (isset($_POST['receive_order'])) {
    $orderId = $_POST['order_id'];
    foreach ($orders as &$order) {
        if ($order['id'] == $orderId && $order['status'] == 'Approved') {
            $order['status'] = 'Received by Canteen';
        }
    }
    file_put_contents('../data/orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    header('Location: canteen_page.php');
}

// Handle Delete Menu Item
if (isset($_POST['delete_menu'])) {
    $deleteId = $_POST['delete_id'];
    $menu = array_filter($menu, fn($m) => $m['id'] != $deleteId);
    file_put_contents('../data/menu.json', json_encode(array_values($menu), JSON_PRETTY_PRINT));
    header('Location: canteen_page.php');
}

// Handle Edit Menu Item
if (isset($_POST['edit_menu'])) {
    $editId = $_POST['edit_id'];
    foreach ($menu as &$m) {
        if ($m['id'] == $editId) {
            $m['meal_type'] = $_POST['edit_meal_type'];
            $m['items'] = $_POST['edit_items'];
            $m['price'] = $_POST['price'];
        }
    }
    file_put_contents('../data/menu.json', json_encode($menu, JSON_PRETTY_PRINT));
    header('Location: canteen_page.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Dashboard</title>
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
        <h2 class="text-3xl font-semibold text-blue-700">Canteen Dashboard</h2>
        <nav class="space-x-4">
            <a href="#add_menu_item" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Menu Item</a>
            <a href="#current_menu" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Current Menu</a>
            <a href="../logout.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Logout</a>
        </nav>
    </div>

    <div id="add_menu_item" class="mb-8 p-6 bg-white rounded-lg shadow-md hidden">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Add Menu Item</h3>
            <a href="canteen_page.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Back to Dashboard</a>
        </div>
        <form method="post" class="space-y-4">
            <div>
                <label for="meal_type" class="block text-gray-700 text-sm font-bold mb-2">Meal Type:</label>
                <input type="text" name="meal_type" id="meal_type" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="items" class="block text-gray-700 text-sm font-bold mb-2">Items (comma separated):</label>
                <input type="text" name="items" id="items" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price (per person):</label>
                <input type="number" name="price" id="price" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" name="add_menu" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Menu</button>
        </form>
    </div>

    <div id="current_menu" class="mb-8 p-6 bg-white rounded-lg shadow-md hidden">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-semibold text-gray-800">Current Menu</h3>
             <a href="canteen_page.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Back to Dashboard</a>
        </div>
        <?php foreach ($menu as $m): ?>
            <div class="p-4 bg-gray-50 rounded-lg shadow-md mb-4 flex justify-between items-center">
                <div>
                    <p class="text-gray-700">Meal Type: <span class="font-semibold"><?= $m['meal_type'] ?></span></p>
                    <p class="text-gray-700">Items: <span class="font-semibold"><?= $m['items'] ?></span></p>
                    <p class="text-gray-700">Price: <span class="font-semibold">Rs.<?= $m['price'] ?></span></p>
                </div>
                <div class="space-x-2">
                    <button onclick="openEditMenu('<?= $m['id'] ?>', '<?= $m['meal_type'] ?>', '<?= $m['items'] ?>', '<?= $m['price'] ?>')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Edit</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                        <button type="submit" name="delete_menu" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="edit_menu_item" class="mb-8 p-6 bg-white rounded-lg shadow-md hidden">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Edit Menu Item</h3>
            <a href="canteen_page.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Back to Dashboard</a>
        </div>
        <form method="post" class="space-y-4">
            <div>
                <label for="edit_meal_type" class="block text-gray-700 text-sm font-bold mb-2">Meal Type:</label>
                <input type="text" name="edit_meal_type" id="edit_meal_type" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="edit_items" class="block text-gray-700 text-sm font-bold mb-2">Items (comma separated):</label>
                <input type="text" name="edit_items" id="edit_items" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="edit_price" class="block text-gray-700 text-sm font-bold mb-2">Price (per person):</label>
                <input type="number" name="edit_price" id="edit_price" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <input type="hidden" name="edit_id" id="edit_id">
            <button type="submit" name="edit_menu" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Menu</button>
        </form>
    </div>

    <div class="mb-8">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Orders (Approved)</h3>
        <?php foreach ($orders as $order): ?>
            <?php if ($order['status'] == 'Approved'): ?>
                <div class="p-4 bg-white rounded-lg shadow-md mb-4">
                    <p class="text-gray-700">Order ID: <span class="font-semibold"><?= $order['id'] ?></span></p>
                    <p class="text-gray-700">User: <span class="font-semibold"><?= $order['username'] ?></span></p>
                    <p class="text-gray-700">Visitors: <span class="font-semibold"><?= $order['visitors'] ?></span></p>
                    <p class="text-gray-700">Meal Type: <span class="font-semibold"><?= $order['meal_type'] ?></span></p>
                    <p class="text-gray-700">Total Price: <span class="font-semibold">Rs.<?= $order['total_price'] ?></span></p>
                    <form method="post" class="mt-4">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="receive_order" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Mark as Received</button>
                    </form>
                </div>
            <?php endif; ?>
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

        function openEditMenu(id, meal_type, items, price) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_meal_type').value = meal_type;
            document.getElementById('edit_items').value = items;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_menu_item').classList.remove('hidden');
             document.querySelectorAll('.mb-8').forEach(section => {
                        section.classList.add('hidden');
                    });
        }
    </script>
</body>
</html>
