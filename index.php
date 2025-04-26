<?php
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $users = json_decode(file_get_contents('data/users.json'), true);

    foreach ($users as $user) {
        if ($user['username'] == $username && $user['password'] == $password && $user['role'] == $role) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role == 'admin') {
                header('Location: admin_page.php');
            } elseif ($role == 'canteen') {
                header('Location: canteen_page.php');
            } elseif ($role == 'user') {
                header('Location: user_page.php');
            }
            exit();
        }
    }
    echo "<script>alert('Invalid Credentials');window.location.href='public/index.html';</script>";
}
?>

