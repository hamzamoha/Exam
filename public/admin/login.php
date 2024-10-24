<?php
session_start();
const USERNAME = "login";
const PASSWORD = "1234";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        if ($_POST['username'] == USERNAME && $_POST["password"] == PASSWORD) {
            $_SESSION['teacher'] = USERNAME;
        }
    }
}
if (isset($_SESSION['teacher']) && $_SESSION['teacher'] == USERNAME) {
    exit(header("location: /admin"));
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
        <input class="bg-slate-100 border border-slate-200 px-2 py-1" type="text" name="username" placeholder="Username" id="username">
        <input class="bg-slate-100 border border-slate-200 px-2 py-1" type="password" name="password" placeholder="Password" id="password">
        <input class="bg-slate-200 border border-slate-200 px-2 py-1 hover:bg-slate-100 cursor-pointer" type="submit" value="Login" name="login">
    </form>
</body>

</html>