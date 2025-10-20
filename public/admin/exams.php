<?php
include "check-admin.php";
$exams = $db->query('SELECT * FROM exams left join (SELECT exam_id, count (Distinct student_id) count FROM submissions GROUP BY exam_id) on id = exam_id Where teacher_id = \'' . $teacher['id'] . "'");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams List | Teacher Dashboard</title>
    <link rel="stylesheet" href="/style.css">
</head>

<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <div class="flex mb-5 items-center p-5">
                <h1 class="text-5xl font-bold">Exams List</h1>
                <a class="ml-auto block py-2 px-4 bg-green-600 rounded text-white font-bold hover:bg-green-500" href="add.php">+ Add</a>
            </div>
            <div class="p-5 rounded-xl bg-white">
                <?php if ($exams->fetchArray()) {
                    $exams->reset(); ?>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-400">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">الاختبار</th>
                                <th class="px-6 py-3">عدد الممررين</th>
                                <th class="px-6 py-3">فتح التمرير</th>
                                <th class="px-6 py-3">تم التصحيح</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($exam = $exams->fetchArray()) {
                                $students_count = $db->query(query: 'SELECT count(*) c FROM students WHERE class in (SELECT class FROM exams_class WHERE exam_id = ' . $exam["id"] . ')')->fetchArray()['c'];
                            ?>
                                <tr class="border-b bg-gray-800 border-gray-700">
                                    <th class="px-6 py-4 font-medium whitespace-nowrap text-white"><?= $exam['id'] ?></th>
                                    <td class="px-6 py-4"><?= $exam['title'] ?></td>
                                    <td class="px-6 py-4" dir="ltr">
                                        <?= ($exam['count'] ?? 0) . ' / ' . $students_count ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="has-[:checked]:bg-blue-400 has-[:checked]:pl-[22px] h-5 w-10 rounded-full bg-gray-500 transition-all block flex p-0.5">
                                            <div class="h-4 w-4 bg-gray-700 rounded-full"></div>
                                            <input class="peer hidden" type="checkbox" <?= $exam['visible'] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="has-[:checked]:bg-green-400 has-[:checked]:pl-[22px] h-5 w-10 rounded-full bg-gray-500 transition-all block flex p-0.5">
                                            <div class="h-4 w-4 bg-gray-700 rounded-full"></div>
                                            <input class="peer hidden" type="checkbox" <?= $exam['graded'] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="inline-block text-xs py-1 px-2 rounded bg-emerald-500 text-white" href="/admin/view.php?id=<?= $exam['id'] ?>">View &raquo;</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="py-14 text-4xl font-bold text-center">You Haven't Created Any Exams Yet!</div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>