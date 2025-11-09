<?php
include "check-admin.php";
$classes = $db->query("SELECT distinct class FROM teachers_class WHERE teacher_id = '" . $teacher['id'] . "'");
if (isset($_GET["class"])) {
    $class = $db->real_escape_string($_GET['class']);
    $case = "";
    $graded_exams = $db->query("SELECT id, title, points FROM exams JOIN (SELECT exam_id, sum(points) points FROM questions GROUP BY exam_id) as o ON exams.id = o.exam_id WHERE graded = 1 and id in (select exam_id from exams_class where class = '$class')");
    $graded_exams2 = $graded_exams;
    while ($graded_exam = $graded_exams->fetch_assoc()) {
        $id = $graded_exam['id'];
        $case .= "MAX(CASE WHEN exam_id = $id THEN s ELSE -1 END) AS EXAM_$id, ";
    }
    $results = $db->query("SELECT $case id, first_name, last_name, student_number, class FROM students LEFT JOIN (SELECT exam_id, student_id, sum(score) as s FROM submissions GROUP BY student_id, exam_id) as p ON students.id = p.student_id GROUP BY id, first_name, last_name, student_number, class HAVING class = '$class'");
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

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
                <form action="?" class="flex justify-center items-center text-center gap-5 mb-5">
                    <select name="class" id="class" class="py-2 px-2 w-32 bg-gray-200 rounded">
                        <option selected disabled>--</option>
                        <?php while ($class1 = $classes->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($class1['class']) ?>"><?= $class1['class'] ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="py-2 px-4 rounded bg-teal-400">Search</button>
                </form>
                <script id="script99858">
                    document.querySelector("select#class").value = "<?= $class ?>"
                    document.querySelector("script#script99858").remove();
                </script>
                <?php if (isset($results) && $graded_exams->num_rows > 0) { ?>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-400">
                            <tr>
                                <th class="px-3 py-2.5 border border-gray-700">First Name</th>
                                <th class="px-3 py-2.5 border border-gray-700">Last Name</th>
                                <?php while ($graded_exam = $graded_exams->fetch_assoc()) { ?>
                                    <th class="px-3 py-2.5 border border-gray-700"><?= $graded_exam['title'] ?> <a href="/admin/export.php?id=<?= $graded_exam['id'] ?>&class=<?= $class ?>&type=grade" class="group text-xs inline-block text-emerald-400"><span class="icon-file-excel"></span>csv</a></th>
                                <?php }
                                $graded_exams->reset(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($result = $results->fetch_assoc()) { ?>
                                <tr class="border-b bg-gray-800 border-gray-700">
                                    <td class="px-3 py-2.5 border border-gray-700"><?= $result['first_name'] ?></td>
                                    <td class="px-3 py-2.5 border border-gray-700"><?= $result['last_name'] ?></td>
                                    <?php while ($graded_exam = $graded_exams->fetch_assoc()) { ?>
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