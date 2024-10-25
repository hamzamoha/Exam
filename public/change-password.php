<?php
include "check-login.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['current_password'])) {
        if ($student['password'] == $_POST['current_password']) {
            if ($_POST['new_password'] == $_POST['new_password_confirmation']) {
                if (strlen($_POST['new_password']) > 0) {
                    $new_password = SQLite3::escapeString($_POST['new_password']);
                    $db->exec("UPDATE students SET password = '$new_password' WHERE id = " . $student['id']);
                    $message = "Le mot de passe a été modifié avec succès!";
                } else $error = "Le mot de passe ne peut être vide!";
            } else $error = "Confirmation de nouveau mot de passe incorrect!";
        } else $error = "Mot de passe incorrect!";
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
    <style>
        body {
            background: url('/imgs/wall.jpg');
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>
        if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <?php include "topnav.php"; ?>
    <div class="p-10">
        <div class="p-5 rounded-xl bg-white shadow-lg">
            <h2 class="text-center text-3xl font-bold py-2">Changer le Mot de Passe</h2>
            <?php if (isset($error)) { ?>
                <div class="py-2 text-red-500 text-center text-xl font-bold"><?= $error ?></div>
            <?php } ?>
            <?php if (isset($message)) { ?>
                <div class="py-2 text-green-500 text-center text-xl font-bold"><?= $message ?></div>
            <?php } ?>
            <div class="flex justify-center py-2">
                <form action="?" method="post" class="max-w-full w-[600px]">
                    <div class="my-5">
                        <div class="font-bold py-1 text-gray-500">Mot de Passe Actuel</div>
                        <input name="current_password" type="password" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" required>
                    </div>
                    <div class="my-5">
                        <div class="font-bold py-1 text-gray-500">Nouveau Mot de Passe</div>
                        <input name="new_password" type="password" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none">
                    </div>
                    <div class="my-5">
                        <div class="font-bold py-1 text-gray-500">Confirmation de Nouveau Mot de Passe</div>
                        <input name="new_password_confirmation" type="password" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="py-2 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>