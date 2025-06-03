<?php
include "check-admin.php";
$case = "";
$graded_exams = $db->query("SELECT id, title, points FROM exams JOIN (SELECT exam_id, sum(points) points FROM questions GROUP BY exam_id) ON id = exam_id WHERE graded = 1");
while ($graded_exam = $graded_exams->fetchArray()) {
    $id = $graded_exam['id'];
    $case .= "MAX(CASE WHEN exam_id = $id THEN s ELSE -1 END) AS EXAM_$id, ";
}
$graded_exams->reset();
$results = $db->query("SELECT $case id, first_name, last_name, student_number FROM students LEFT JOIN (SELECT exam_id, student_id, count(*) as p, sum(score) as s FROM submissions GROUP BY student_id, exam_id) ON id = student_id GROUP BY id, first_name, last_name, student_number");
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
            <div class="flex mb-5 items-center p-5">
                <h1 class="text-5xl font-bold">Results</h1>
            </div>
            <div class="bg-white p-5 rounded-xl">
                <?php if ($graded_exams->fetchArray()) {
                    $graded_exams->reset(); ?>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-400">
                            <tr>
                                <th class="px-3 py-2.5 border border-gray-700">First Name</th>
                                <th class="px-3 py-2.5 border border-gray-700">Last Name</th>
                                <?php while ($graded_exam = $graded_exams->fetchArray()) { ?>
                                    <th class="px-3 py-2.5 border border-gray-700"><?= $graded_exam['title'] ?> <a href="/admin/export.php?id=<?= $graded_exam['id'] ?>&type=grade" class="group text-xs inline-block text-emerald-400"><span class="icon-file-excel"></span>csv</a></th>
                                <?php }
                                $graded_exams->reset(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($result = $results->fetchArray()) { ?>
                                <tr class="border-b bg-gray-800 border-gray-700">
                                    <td class="px-3 py-2.5 border border-gray-700"><?= $result['first_name'] ?></td>
                                    <td class="px-3 py-2.5 border border-gray-700"><?= $result['last_name'] ?></td>
                                    <?php while ($graded_exam = $graded_exams->fetchArray()) { ?>
                                        <td class="px-3 py-2.5 border border-gray-700">
                                            <?php if ($result['EXAM_' . $graded_exam['id']] == -1) { ?>
                                                <span class="inline-block py-0.5 px-1 text-xs text-white bg-rose-600 whitespace-nowrap rounded-full">Not Passed</span>
                                            <?php } else { ?>
                                                <?= $result['EXAM_' . $graded_exam['id']] . " / " . $graded_exam['points'] ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="py-14 text-4xl font-bold text-center">No Graded Exams Yet!</div>
                <?php } ?>
            </div>
        </div>
    </div>


</body>

</html>