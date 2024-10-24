<?php
include "check-admin.php";
$results = $db->query('SELECT * FROM exams');
$exam = $results->fetchArray();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = SQLite3::escapeString($_POST['title']);
    $description = SQLite3::escapeString($_POST['description']);
    $duration_minutes = SQLite3::escapeString($_POST['duration']);
    $db->exec("INSERT INTO exams (title, description, duration_minutes) VALUES ('$title','$description','$duration_minutes')");
    header("location: ./edit.php?id=" . $db->lastInsertRowID());
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
            <form class="bg-white p-2" action="?" method="post">
                <h2 class="text-3xl mb-2">Create an Exam</h2>
                <div class="mb-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            Title
                            <input name="title" type="text" class="rounded py-1.5 px-2 w-full bg-slate-200 border border-slate-400" required>
                        </div>
                        <div>
                            Duration (in minutes)
                            <input name="duration" type="number" class="rounded py-1.5 px-2 w-full bg-slate-200 border border-slate-400">
                        </div>
                    </div>
                    Description
                    <input name="description" type="text" class="rounded py-1.5 px-2 w-full bg-slate-200 border border-slate-400">
                </div>
                <div class="text-center">
                    <button type="submit" class="rounded bg-slate-200 p-2 border border-slate-400 hover:bg-slate-100">Save and go to Questions</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>