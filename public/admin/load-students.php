<?php
include "check-admin.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['load'])) {
        $file = $_FILES["xlsx"];
        include "../../SimpleXLSX.php";
        if ($xlsx = SimpleXLSX::parse($file['tmp_name'])) {
            $students_count = $db->query(query: 'SELECT count(*) c FROM students')->fetchArray()['c'];
            $sheets_count = $xlsx->sheetsCount();
            for ($i = 0; $i < $sheets_count; $i++) {
                $class = SQLite3::escapeString($xlsx->rows($i)[7][2]);
                $rows_count = count($xlsx->rows($i));
                $query = "INSERT INTO students (student_number, first_name, last_name, gender, date_of_birth, place_of_birth, class) VALUES ";
                for ($j = 10; $j < $rows_count; $j++) {
                    $code_massar = SQLite3::escapeString($xlsx->rows($i)[$j][1]);
                    $last_name = SQLite3::escapeString($xlsx->rows($i)[$j][2]);
                    $first_name = SQLite3::escapeString($xlsx->rows($i)[$j][3]);
                    $gender = SQLite3::escapeString($xlsx->rows($i)[$j][4]);
                    $date_of_birth = SQLite3::escapeString($xlsx->rows($i)[$j][5]);
                    $place_of_birth = SQLite3::escapeString($xlsx->rows($i)[$j][6]);
                    $query .= "('$code_massar', '$first_name', '$last_name', '$gender', '$date_of_birth', '$place_of_birth', '$class'), ";
                }
                $query = substr($query, 0, -2);
                try {
                    $db->exec($query);
                } catch (Throwable $th) {
                }
            }
            $students_count = $db->query(query: 'SELECT count(*) c FROM students')->fetchArray()['c'] - $students_count;
        }
    }
}
if (isset($_GET['class'])) {
    $class = SQLite3::escapeString($_GET['class']);
    $students = $db->query("SELECT * FROM students where class = '$class'");
}
$classes = $db->query('SELECT class FROM students group by class'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load Students | Teacher Dashboard</title>
    <link rel="stylesheet" href="/style.css">
    <script>
        if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <div class="mb-5 flex justify-center items-center gap-5">
                <form action="?" method="post" class="w-full text-center py-20 px-5 rounded bg-white" enctype="multipart/form-data">
                    <h2 class="text-3xl font-medium mb-5">Load Students</h2>
                    <h3 class="text-xl font-medium mb-5">Upload a .XLSX File (From Massar)</h3>
                    <input type="file" required name="xlsx" id="xlsx" class="p-1 bg-slate-300 rounded cursor-pointer">
                    <input type="submit" name="load" value="Load" class="py-2 px-4 rounded bg-slate-800 text-white cursor-pointer hover:bg-slate-700">
                    <?php if (isset($students_count)) { ?>
                        <div class="py-5 text-xl font-bold text-green-500">+<?= $students_count ?> Students added</div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>