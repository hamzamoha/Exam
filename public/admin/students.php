<?php
include "check-admin.php";
if (isset($_GET['class'])) {
    $class = SQLite3::escapeString($_GET['class']);
    $students = $db->query("SELECT * FROM students where class = '$class' AND class in (SELECT distinct class from teachers_class where teacher_id = '$teacher[id]')");
}
$classes = $db->query('SELECT class FROM students group by class having class in (SELECT distinct class from teachers_class where teacher_id = \'' . $teacher['id'] . '\')'); ?>
<!DOCTYPE html>
<html lang="en">

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
            <div class="flex mb-5 items-center p-5">
                <h1 class="text-5xl font-bold">Students List</h1>
                <a href="load-students.php" class="block ml-auto py-2 px-4 rounded bg-slate-600 text-white font-medium hover:bg-slate-500 transition-all">Load Students</a>
            </div>
            <form action="?" class="bg-white p-5 rounded-xl flex justify-center items-center text-center gap-5 mb-5">
                <select name="class" id="class" class="py-2 px-2 w-32 bg-gray-200 rounded">
                    <option selected disabled>--</option>
                    <?php while ($class = $classes->fetchArray()) { ?>
                        <option value="<?= htmlspecialchars($class['class']) ?>"><?= $class['class'] ?></option>
                    <?php } ?>
                </select>
                <button type="submit" class="py-2 px-4 rounded bg-teal-400">Search</button>
            </form>
            <?php if (isset($students)) { ?>
                <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-400">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Student Number</th>
                            <th class="px-6 py-3">First Name</th>
                            <th class="px-6 py-3">Last Name</th>
                            <th class="px-6 py-3">Date of Birth</th>
                            <th class="px-6 py-3">Place of Birth</th>
                            <th class="px-6 py-3">Class</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $c = 1;
                        while ($student = $students->fetchArray()) { ?>
                            <tr class="border-b bg-gray-800 border-gray-700">
                                <th class="px-6 py-4 font-medium whitespace-nowrap text-white"><?= $c++ ?></th>
                                <td class="px-6 py-4"><?= $student['student_number'] ?></td>
                                <td class="px-6 py-4"><?= $student['first_name'] ?></td>
                                <td class="px-6 py-4"><?= $student['last_name'] ?></td>
                                <td class="px-6 py-4"><?= $student['date_of_birth'] ?></td>
                                <td class="px-6 py-4"><?= $student['place_of_birth'] ?></td>
                                <td class="px-6 py-4"><?= $student['class'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
</body>

</html>