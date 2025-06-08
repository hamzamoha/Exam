<?php
session_start();
$db = new SQLite3('../../db.db');
$db->enableExceptions(true);
if (isset($_SESSION['teacher'])) {
    $id = SQLite3::escapeString($_SESSION['teacher']);
    $id = $db->query("SELECT id from teachers WHERE id = '$id'")->fetchArray();
    if ($id) {
        exit(header("location: /admin"));
    }
}
$err = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = SQLite3::escapeString($_POST['username']);
        $password = SQLite3::escapeString($_POST['password']);
        $result = $db->query("SELECT id, username, password FROM teachers WHERE username = '$username'");
        if ($user = $result->fetchArray()) {
            if ($user['password'] == $password) {
                $_SESSION['teacher'] = $user['id'];
                header("location: /admin");
            } else $err = 2; // pass
        } else $err = 1; // user
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/style.css">
</head>

<body class="flex justify-center items-center min-h-screen flex-col gap-5">
    <h1 class="text-3xl">Teacher Login</h1>
    <form action="?" method="post" class="flex flex-col gap-5">
        <input class="bg-slate-100 border border-slate-200 px-2 py-1" type="text" name="username" placeholder="Username" id="username" value="<?= $err == 2 ? htmlspecialchars($_POST['username']) : "" ?>">
        <input class="bg-slate-100 border border-slate-200 px-2 py-1" type="password" name="password" placeholder="Password" id="password">
        <input class="bg-slate-200 border border-slate-200 px-2 py-1 hover:bg-slate-100 cursor-pointer" type="submit" value="Login" name="login">
        <?php if ($err > 0) { ?>
            <p class="text-red-500 text-center py-2">
                <?= $err == 2 ? "Wrong Password" : "Wrong Username" ?>
            </p>
        <?php } ?>
    </form>
</body>

</html>