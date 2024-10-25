<?php
include "check-admin.php";
$results = $db->query('SELECT * FROM exams');
$exam = $results->fetchArray();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = SQLite3::escapeString($_POST['title']);
    $description = SQLite3::escapeString($_POST['description']);
    $duration_minutes = SQLite3::escapeString($_POST['duration']);
    $db->exec("INSERT INTO exams (title, description, duration_minutes) VALUES ('$title','$description','$duration_minutes')");
    exit(header("location: /admin/edit.php?id=" . $db->lastInsertRowID()));
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

<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <form class="bg-white p-5 rounded-xl" action="?" method="post">
                <h2 class="text-3xl mb-2">Create an Exam</h2>
                <hr class="my-5">
                <div class="mb-2">  
                    <div class="grid grid-cols-2 gap-2 my-6">
                        <div>
                            <div class="font-bold py-1 text-gray-500">Title</div>
                            <input name="title" type="text" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" required>
                        </div>
                        <div>
                            <div class="font-bold py-1 text-gray-500">Duration (in minutes)</div>
                            <input name="duration" type="number" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" required value="30">
                        </div>
                    </div>
                    <div class="my-6">
                        <div class="font-bold py-1 text-gray-500">Description</div>
                        <input name="description" type="text" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none">
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="rounded bg-indigo-600 py-2 px-4 hover:bg-indigo-500 text-white">Save and go to Questions</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>