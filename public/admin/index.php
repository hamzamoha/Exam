<?php
include "check-admin.php";
$students_count = $db->query(query: 'SELECT count(*) c FROM students')->fetchArray()['c'];
$exams_count = $db->query(query: 'SELECT count(*) c FROM exams where teacher_id = \'' . $teacher['id'] . "'")->fetchArray()['c'];
$classes_count = $db->query(query: 'SELECT count(distinct class) c FROM students')->fetchArray()['c'];
$age_count = $db->query("SELECT strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) AS age, COUNT(*) AS count FROM students GROUP BY age");
$max = $db->query("SELECT MAX(student_count) AS max FROM (SELECT strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) AS age, COUNT(*) AS student_count FROM students GROUP BY age)")->fetchArray()['max'];
$gender_count = $db->query("SELECT gender, COUNT(*) AS count FROM students GROUP BY gender");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Teacher</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/charts.min.css">
</head>

<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <div class="bg-white p-5 rounded-xl">
                <div class="grid grid-cols-3 gap-5 text-white">
                    <div class="flex items-center bg-gradient-to-l from-green-500 to-green-300 rounded-lg p-2">
                        <span class="icon-user text-5xl p-5"></span>
                        <div>
                            <h3 class="text-5xl font-bold"><?= $students_count ?></h3>
                            <h3 class="text-xl font-bold">المتعلمين</h3>
                        </div>
                    </div>
                    <div class="flex items-center bg-gradient-to-l from-rose-500 to-rose-300 rounded-lg p-2">
                        <span class="icon-chalk-board text-5xl p-5"></span>
                        <div>
                            <h3 class="text-5xl font-bold"><?= $classes_count ?></h3>
                            <h3 class="text-xl font-bold">الأقسام</h3>
                        </div>
                    </div>
                    <div class="flex items-center bg-gradient-to-l from-cyan-500 to-cyan-300 rounded-lg p-2">
                        <span class="icon-stack text-5xl p-5"></span>
                        <div>
                            <h3 class="text-5xl font-bold"><?= $exams_count ?></h3>
                            <h3 class="text-xl font-bold">الاختبارات</h3>
                        </div>
                    </div>
                </div>
                <?php if ($students_count > 0) { ?>
                    <div class="grid grid-cols-2 gap-3 my-10">
                        <div>
                            <div>
                                <div class="mb-2 py-5 px-52 rounded border bg-gray-100">
                                    <div class="text-center mb-2">المتعلمين حسب الجنس</div>
                                    <table class="charts-css pie show-heading">
                                        <tbody>
                                            <?php $start = 0;
                                            while ($rec = $gender_count->fetchArray()) { ?>
                                                <tr>
                                                    <td style="--start: <?= $start ?>; --end: <?= $start += $percent = round(intval($rec['count']) / intval($students_count), 2) ?>;"><span class="data"><?= $percent * 100 ?>%</span></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <ul class="charts-css legend legend-rectangle legend-inline justify-center flex-row-reverse">
                                    <?php while ($rec = $gender_count->fetchArray()) { ?>
                                        <li><?= $rec['gender'] ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="mb-2 py-6 px-44 rounded border bg-gray-100">
                                    <div class="text-center mb-2">المتعلمين حسب العمر</div>
                                    <table class="charts-css column show-labels" style="--aspect-ratio: 1/1">
                                        <tbody>
                                            <?php while ($rec = $age_count->fetchArray()) { ?>
                                                <tr>
                                                    <th scope="row"><?= $rec['age'] ?></th>
                                                    <td style="--size: <?= round(intval($rec['count']) / intval($max), 2) ?>"></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="flex justify-center flex-wrap">
                                <?php
                                $classes = $db->query("SELECT distinct class from students");
                                while ($class = $classes->fetchArray()) {
                                    $class = $class["class"]; ?>
                                    <div class="w-1/6 p-0.5">
                                        <div class="p-5 rounded border bg-gray-100">
                                            <div class="text-center mb-2"><?= $class ?></div>
                                            <table class="charts-css pie show-heading text-xs">
                                                <tbody>
                                                    <?php $start = 0;
                                                    $gender_count = $db->query("SELECT gender, COUNT(*) AS count FROM students GROUP BY gender, class having class = '$class'");
                                                    $students_count = $db->query("SELECT count(*) c FROM students WHERE class = '$class'")->fetchArray()['c'];
                                                    while ($rec = $gender_count->fetchArray()) { ?>
                                                        <tr>
                                                            <td style="--start: <?= $start ?>; --end: <?= $start += $percent = round(intval($rec['count']) / intval($students_count), 2) ?>;"><span class="data"><?= $percent * 100 ?>%</span></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="">
                            <div class="flex justify-center flex-wrap">
                                <?php
                                $classes = $db->query("SELECT distinct class from students");
                                while ($class = $classes->fetchArray()) {
                                    $class = $class["class"]; ?>
                                    <div class="w-1/6 p-0.5">
                                        <div class="p-5 rounded border bg-gray-100">
                                            <div class="text-center mb-2"><?= $class ?></div>
                                            <table class="charts-css column show-labels" style="--aspect-ratio: 1/1">
                                                <tbody>
                                                    <?php
                                                    $age_count = $db->query("SELECT strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) AS age, COUNT(*) AS count FROM students GROUP BY age, class HAVING class = '$class'");
                                                    $max = $db->query("SELECT MAX(student_count) AS max FROM (SELECT strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) AS age, COUNT(*) AS student_count FROM students GROUP BY age, class HAVING class = '$class')")->fetchArray()['max'];
                                                    while ($rec = $age_count->fetchArray()) { ?>
                                                        <tr>
                                                            <th scope="row" class="text-[10px]"><?= $rec['age'] ?></th>
                                                            <td style="--size: <?= round(intval($rec['count']) / intval($max), 2) ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>