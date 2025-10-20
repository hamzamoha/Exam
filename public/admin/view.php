<?php
include "check-admin.php";
$exam_id = SQLite3::escapeString($_GET['id']);
$ungraded_count = intval($db->query("SELECT count(*) c FROM submissions WHERE score = -1 AND exam_id = '$exam_id'")->fetchArray()['c']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['visible'])) $db->exec("UPDATE exams SET visible = (visible + 1)%2 WHERE id = '$exam_id'");
    if (isset($_POST['graded']))
        if ($ungraded_count > 0) $db->exec("UPDATE exams SET graded = 0 WHERE id = '$exam_id'");
        else $db->exec("UPDATE exams SET graded = (graded + 1)%2 WHERE id = '$exam_id'");
}
$results = $db->query("SELECT * FROM exams left join (SELECT exam_id, count (Distinct student_id) count FROM submissions GROUP BY exam_id) s on id = s.exam_id left join (SELECT exam_id, count (*) q_count, sum (points) p_sum FROM questions GROUP BY exam_id) q on id = q.exam_id WHERE id = '$exam_id'");
$exam = $results->fetchArray();
if (!$exam || $exam['teacher_id'] != $teacher['id']) exit(header("location: /admin/exams.php"));
$questions = $db->query("SELECT * FROM questions WHERE exam_id = '$exam_id'");
$students_count = $db->query(query: 'SELECT count(*) c FROM students WHERE class IN (SELECT class FROM exams_class WHERE exam_id = \'' . $exam_id . '\')')->fetchArray()['c'];
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

            <div class="bg-white p-8 rounded-xl mb-5">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold"><?= $exam['title'] ?></h1>
                    <h2 class="text-base text-gray-500">Created at: <?= explode(" ", $exam['created_at'])[0] ?></h2>
                    <div>Classes:
                        <?php
                        $exam_classes = $db->query("SELECT class FROM exams_class WHERE exam_id = '$exam_id'");
                        while ($class = $exam_classes->fetchArray()) { ?>
                            <span class="inline-block text-sm rounded border p-1 bg-gray-100"><?= $class['class'] ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="w-full flex items-center">
                    <div class="flex-1 font-bold flex items-center">
                        <span class="icon-file-text p-4 text-blue-500"></span>
                        <div>
                            <div class="mb-1"><?= $exam['q_count'] ?? 0 ?> Questions</div>
                            <div class="text-sm text-gray-500">Number of Questions</div>
                        </div>
                    </div>
                    <div class="w-12 h-8">
                        <div class="w-px bg-slate-400 h-full mx-auto"></div>
                    </div>
                    <div class="flex-1 font-bold flex items-center">
                        <span class="icon-clock2 p-4 text-amber-500"></span>
                        <div>
                            <div class="mb-1"><?= $exam['duration_minutes'] ?> minutes</div>
                            <div class="text-sm text-gray-500">Exam's Duration</div>
                        </div>
                    </div>
                    <div class="w-12 h-8">
                        <div class="w-px bg-slate-400 h-full mx-auto"></div>
                    </div>
                    <div class="flex-1 font-bold flex items-center">
                        <span class="icon-user p-4 text-green-500"></span>
                        <div>
                            <div class="mb-1"><?= $exam['p_sum'] ?? 0 ?> Points</div>
                            <div class="text-sm text-gray-500">Exam's Points</div>
                        </div>
                    </div>
                    <div class="w-12 h-8">
                        <div class="w-px bg-slate-400 h-full mx-auto"></div>
                    </div>
                    <div class="flex-1 font-bold flex items-center">
                        <span class="icon-user p-4 text-green-500"></span>
                        <div>
                            <div class="mb-1"><?= ($exam['count'] ?? 0) . ' / ' . $students_count ?></div>
                            <div class="text-sm text-gray-500">Number of Participants</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($ungraded_count > 0) { ?>
                <div class="mb-5 justify-end flex gap-2 items-center">
                    <span class="text-red-500"><?= $ungraded_count ?> Ungraded Answers</span>
                    <a href="grade.php?id=<?= $exam_id ?>" class="py-2 px-5 rounded-full bg-rose-600 text-white hover:bg-rose-500">Grade Now !</a>
                </div>
            <?php } ?>
            <div class="bg-white p-8 rounded-xl">
                <div class="mb-5 flex gap-5">
                    <a class="block py-2 text-sm px-5 rounded-full bg-orange-500 text-white flex items-center" href="modify.php?id=<?= $exam['id'] ?>">
                        <span class="icon-pencil mr-2 text-white"></span>
                        Modify
                    </a>
                    <a class="block py-2 text-sm px-5 rounded-full bg-lime-600 text-white flex items-center" href="edit.php?id=<?= $exam['id'] ?>">
                        <span class="icon-edit-list mr-2 text-white"></span>
                        Edit Questions
                    </a>
                    <form action="?id=<?= $exam['id'] ?>" method="post">
                        <button type="submit" name="visible" class="<?= $exam['visible'] == 1 ? "bg-sky-600 hover:bg-gray-400" : "bg-gray-400 hover:bg-sky-600" ?> group transition-all py-2 text-sm px-5 rounded-full text-white flex items-center">
                            <span class="<?= $exam['visible'] == 1 ? "icon-eye" : "icon-eye-blocked" ?> mr-2 text-white group-hover:hidden"></span>
                            <span class="<?= $exam['visible'] == 1 ? "icon-eye-blocked" : "icon-eye" ?> mr-2 text-white group-hover:block hidden"></span>
                            Visible
                        </button>
                    </form>
                    <form action="?id=<?= $exam['id'] ?>" method="post">
                        <button type="submit" name="graded" class="<?= $exam['graded'] == 1 ? "bg-indigo-600 hover:bg-gray-400" : "bg-gray-400 hover:bg-indigo-600" ?> group transition-all py-2 text-sm px-5 rounded-full text-white flex items-center">
                            <span class="icon-checkbox-checked mr-2 text-white"></span>
                            Graded
                        </button>
                    </form>
                    <a class="ml-auto group block py-2 text-sm px-5 rounded-full bg-emerald-500 text-white flex items-center" href="export.php?id=<?= $exam['id'] ?>">
                        <span class="icon-file-excel mr-2 text-white"></span>
                        Export Students Answers as CSV
                    </a>
                </div>
                <hr class="my-5">
                <h1 class="text-2xl font-bold mb-2">Questions</h1>
                <?php
                while ($question = $questions->fetchArray()) { ?>
                    <div class="py-2">
                        <div>
                            <?php if ($question['type'] == 'mcq') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-amber-500">Multiple Choice</div>
                            <?php } else if ($question['type'] == 'true_false') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-emerald-500">True or False</div>
                            <?php } else if ($question['type'] == 'short_answer') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-rose-500">Short Answer</div>
                            <?php } else if ($question['type'] == 'matching_pairs') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-sky-500">Matching Pairs</div>
                            <?php } ?>
                        </div>
                        <div>
                            <b>Question:</b> <?= $question['question_text'] ?> (<?= $question['points'] ?>p)
                        </div>
                        <div>
                            <b>Correct Answer:</b> <?= $question['correct_answer'] ?>
                        </div>
                        <?php if ($question['type'] == 'mcq') { ?>
                            <div>
                                <b>Options:</b> <?= implode(", ", json_decode($question['options'])->options) ?>
                            </div>
                        <?php } ?>
                        <?php if ($question['type'] == 'matching_pairs') {
                            $pairs = [];
                            $pairss = $db->query("SELECT * FROM matching_pairs WHERE question_id = " . $question['id']);
                            while ($pair = $pairss->fetchArray()) {
                                $pairs[$pair['id']] = $pair;
                            }
                        ?>
                            <div>
                                <b class="block">Pairs:</b>
                                <?php foreach ($pairs as $id => $pair) {
                                    if ($pair['parent_id']) { ?>
                                        <div>
                                            <?= $pair['text'] ?> => <?= $pairs[$pair['parent_id']]['text'] ?>
                                        </div>
                                <?php }
                                } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>

</body>

</html>