<?php
include "check-admin.php";
$id = SQLite3::escapeString($_GET['id']);
$results = $db->query("SELECT * FROM exams WHERE id = '$id'");
$exam = $results->fetchArray();
$questions = $db->query("SELECT * FROM questions WHERE exam_id = '$id' AND type = 'short_answer'");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if (preg_match("/^sub_\d+$/i", $key)) {
            $key = substr($key, 4);
            $value = SQLite3::escapeString($value);
            $db->exec("UPDATE submissions set score = '$value' WHERE id = '$key'");
        }
    }
    exit(header("location: /admin/view.php?id=" . $_GET['id']));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Exam | Teacher</title>
    <link rel="stylesheet" href="/style.css">
</head>

<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <div class="bg-white p-5">
                <div class="flex mb-2">
                    <a href="view.php?id=<?= $id ?>" class="py-1 px-2 rounded bg-amber-500">&laquo; Back</a>
                </div>
                <h1 class="text-2xl font-bold">Grade: <?= $exam['title'] ?></h1>
                <hr class="my-5">
                <form action="?id=<?= $_GET['id'] ?>" method="post">
                    <?php while ($question = $questions->fetchArray()) {
                        $submissions = $db->query("SELECT * FROM submissions WHERE score = -1 AND exam_id = '$id' AND question_id = " . $question['id']); ?>
                        <div>
                            <div class="text-lg"><b>Question: </b><?= $question['question_text'] ?></div>
                            <div class="text-gray-700"><b>Correct Answer: </b><span class="text-green-600"><?= $question['correct_answer'] ?></span></div>
                            <table class="my-5">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-1.5 border">Answer</th>
                                        <th class="px-3 py-1.5 border">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($submission = $submissions->fetchArray()) { ?>
                                        <tr>
                                            <td class="px-3 py-1.5 border"><?= $submission['answer'] ?></td>
                                            <td class="px-3 py-1.5 border">0 <input name="sub_<?= $submission['id'] ?>" type="range" min="0" max="<?= $question['points'] ?>" step="<?= $question['points'] / 4 ?>"> <?= $question['points'] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr class="my-5">
                    <?php } ?>
                    <div>
                        <button type="submit" class="py-2 px-4 rounded bg-green-600 text-white font-semibold text-sm hover:bg-green-500 transition-all">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>