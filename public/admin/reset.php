<?php
include "check-admin.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    if (isset($_POST['reset_db'])) {
        if (isset($_POST['students'])) {
            $db->execute_query("DELETE FROM sessions; DELETE FROM submissions; DELETE FROM students;");
            $db->execute_query("DELETE FROM SQLITE_SEQUENCE WHERE lower(name)='sessions' OR lower(name)='submissions' OR lower(name)='students';");
        }
        if (isset($_POST['exams'])) {
            $db->execute_query("DELETE FROM sessions; DELETE FROM submissions; DELETE FROM matching_pairs; DELETE FROM questions; DELETE FROM exams;");
            $db->execute_query("DELETE FROM SQLITE_SEQUENCE WHERE lower(name)='sessions' OR lower(name)='submissions' OR lower(name)='matching_pairs' OR lower(name)='questions' OR lower(name)='exams';");
        }
    } else if (isset($_POST['delete']))
        if (isset($_POST['session_id'])){
            $db->execute_query("DELETE FROM sessions WHERE id = '" . $db->real_escape_string($_POST['session_id']) . "'");
            header("location: /admin/sessions.php");
        }
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            <form method="post" action="?" class="bg-white p-5 rounded-xl">
                <h1 class="text-5xl font-bold text-red-500 mb-5 text-center">Reset Database</h1>
                <p class="text-xl py-5 text-center">This action cannot be undone, you will delete everything from the database (exams, students, grades, etc.)</p>
                <div class="flex justify-center">
                    <table>
                        <tbody>
                            <tr>
                                <td class="px-2 py-4"><input type="checkbox" name="sessions" id="sessions" class="w-5 h-5 cursor-pointer"></td>
                                <td class="px-2 py-4 text-xl">Sessions</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-4"><input type="checkbox" name="students" id="students" class="w-5 h-5 cursor-pointer"></td>
                                <td class="px-2 py-4 text-xl">Students</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-4"><input type="checkbox" name="exams" id="exams" class="w-5 h-5 cursor-pointer"></td>
                                <td class="px-2 py-4 text-xl">Exams</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <button type="submit" name="reset_db" class="bg-red-600 text-white py-2 px-4 rounded font-bold hover:bg-red-500 transition-all">Reset</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>