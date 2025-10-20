<?php
include "check-login.php";
$exam_id = SQLite3::escapeString($_GET['id']);
$results = $db->query("SELECT * FROM exams WHERE id = '$exam_id'");
if (!($exam = $results->fetchArray())) exit(header('location: /'));
$questions = $db->query("SELECT * FROM questions WHERE exam_id = '$exam_id'");

$student_id = $student['id'];
$results = $db->query("SELECT * FROM SESSIONS WHERE student_id = $student_id AND exam_id = '$exam_id'");
$exam_duration = intval($exam['duration_minutes']);
if (!($session = $results->fetchArray())) {
    $start_at = time();
    $end_at = strtotime("+$exam_duration minutes", time());
    $db->exec("INSERT INTO SESSIONS(student_id, exam_id, start_at, end_at) VALUES ($student_id, '$exam_id', '$start_at', '$end_at')");
    $session = $db->query("SELECT * FROM SESSIONS WHERE student_id = $student_id AND exam_id = '$exam_id'")->fetchArray();
}

$timesup = false;
$end_timestamp = intval($session["END_AT"]);
$start_timestamp = intval($session["START_AT"]);
$remaining_timestamp = $end_timestamp - time();
if ($remaining_timestamp < 0) $timesup = true;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    while ($question = $questions->fetchArray()) {
        $score = 0;
        $answer = SQLite3::escapeString($_POST["answer_" . $question['id']]);
        if ($question['type'] == 'short_answer') $score = -1;
        else if ($question['type'] == 'matching_pairs') {
            $question_id = $question['id'];
            $pairs = $db->query("SELECT * FROM matching_pairs WHERE question_id = $question_id AND parent_id > 0");
            $count = 0;
            $answer = [];
            while ($pair = $pairs->fetchArray()) {
                $count++;
                if ($_POST["right_" . $pair['id']] == $pair["parent_id"]) {
                    $score++;
                }
                $answer[$pair['id']] = $_POST["right_" . $pair['id']];
            }
            $score = $score * $question['points'] / $count;
            $answer = SQLite3::escapeString(json_encode($answer));
        } else if (strtolower($_POST["answer_" . $question['id']]) == strtolower($question['correct_answer'])) $score = $question['points'];

        $db->exec("INSERT INTO submissions (exam_id, student_id, question_id, answer, score) VALUES ('$exam_id', " . $student['id'] . ", " . $question['id'] . ", '" . $answer . "', " . $score . ") ");
    }
    $questions->reset();
    header("location: /");
}
$questions_shuffled = [];
while ($question = $questions->fetchArray()) $questions_shuffled[] = $question;
shuffle($questions_shuffled);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body {
            background: #ffffff;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let end = Date.now() + <?= $remaining_timestamp ?>000;
            let start_at = new Date(end - <?= intval($exam_duration) * 60 ?>000);
            document.querySelector("#time_start").innerHTML = start_at.getHours() + ":" + ("0" + start_at.getMinutes()).substr(-2) + ":" + ("0" + start_at.getSeconds()).substr(-2);
            <?php if (!$timesup) { ?>
                let time_int = setInterval(() => {
                    let start = Date.now();
                    let remain = Math.floor((end - start) / 1000);
                    if (remain < 0) {
                        clearInterval(time_int);
                        document.querySelector("form").submit();
                        return;
                    }
                    document.querySelector("#time_h").innerHTML = Math.floor(remain / 3600);
                    document.querySelector("#time_m").innerHTML = Math.floor(remain % 3600 / 60);
                    document.querySelector("#time_s").innerHTML = Math.floor(remain % 60);
                }, 1000);
            <?php } ?>
        })
    </script>
</head>

<body>
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div class="w-52">
            <div class="bg-white border p-2 mb-5">
                <p class="text-center text-xl">مدة الاختبار</p>
                <p class="text-center py-2"><span class="text-3xl"><?= $exam['duration_minutes'] ?></span> mins</p>
            </div>
            <?php if (!$timesup) { ?>
                <div class="bg-white border p-2 mb-5">
                    <p class="text-center text-xl">الوقت المتبقي</p>
                    <p class="text-center py-2">
                        <span id="time_h" class="text-3xl"><?= intdiv($remaining_timestamp, 3600) ?></span>h
                        <span id="time_m" class="text-3xl"><?= intdiv($remaining_timestamp % 3600, 60) ?></span>m
                        <span id="time_s" class="text-3xl"><?= $remaining_timestamp % 60 ?></span>s
                    </p>
                </div>
            <?php } ?>
            <div class="bg-white border p-2">
                <p class="text-center text-xl">توقيت البداية</p>
                <p class="text-center py-2">
                    <span id="time_start" class="text-3xl"></span>
                </p>
            </div>
        </div>
        <div class="flex-1">
            <?php if ($timesup) { ?>
                <div class="bg-white border py-20 px-10 text-6xl mb-5 text-center text-red-500 font-bold">
                    Le temps est écoulé
                </div>
            <?php } else { ?>
                <div <?= $exam['is_rtl'] == 0 ? 'dir="ltr" ' : "" ?>class="bg-white border p-10">
                    <h1 class="text-3xl font-bold mb-2 text-[#ff8723]"><?= $exam['title'] ?></h1>
                    <hr class="my-5">
                    <form action="?id=<?= $exam_id ?>" method="post">
                        <?php $c = 1;
                        foreach ($questions_shuffled as $c => $question) { ?>
                            <div class="py-2">
                                <h2 class="text-xl mb-2"><b>Question <?= $c+1 ?>: </b><?= $question['question_text'] ?></h2>
                                <?php if ($question['type'] == 'true_false') { ?>
                                    <div class="flex flex-wrap items-center gap-2 my-2">
                                        <label class="border bg-white rounded py-1.5 px-3 hover:bg-neutral-100 has-[:checked]:bg-neutral-600 has-[:checked]:text-white cursor-pointer" for="true_<?= $question['id'] ?>">
                                            <input required class="hidden" type="radio" name="answer_<?= $question['id'] ?>" id="true_<?= $question['id'] ?>" value="true">
                                            True
                                        </label>
                                        <label class="border bg-white rounded py-1.5 px-3 hover:bg-neutral-100 has-[:checked]:bg-neutral-600 has-[:checked]:text-white cursor-pointer" for="false_<?= $question['id'] ?>">
                                            <input required class="hidden" type="radio" name="answer_<?= $question['id'] ?>" id="false_<?= $question['id'] ?>" value="false">
                                            False
                                        </label>
                                    </div>
                                <?php } else if ($question['type'] == 'mcq') {
                                    $options = json_decode($question['options'])->options;
                                ?>
                                    <div class="flex flex-wrap items-center gap-2 my-2">
                                        <?php foreach ($options as $option) { ?>
                                            <label class="border bg-white rounded py-1.5 px-3 hover:bg-neutral-100 has-[:checked]:bg-neutral-600 has-[:checked]:text-white cursor-pointer" for="<?= $option ?> <?= $question['id'] ?>">
                                                <input required class="hidden" type="radio" name="answer_<?= $question['id'] ?>" id="<?= $option ?> <?= $question['id'] ?>" value="<?= $option ?>">
                                                <?= $option ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                <?php } else if ($question['type'] == 'short_answer') { ?>
                                    <input type="text" required name="answer_<?= $question['id'] ?>" placeholder="answer..." class="bg-white border border-neutral-400 rounded shadow-xs py-2.5 px-3.5">
                                    <?php } else if ($question['type'] == 'matching_pairs') {
                                    $left = $db->query("SELECT * FROM matching_pairs WHERE parent_id = 0 AND question_id = " . $question['id'] . " ORDER BY RANDOM()");
                                    $right = $db->query("SELECT * FROM matching_pairs WHERE parent_id > 0 AND question_id = " . $question['id'] . " ORDER BY RANDOM()");
                                    while ($right_ele = $right->fetchArray()) { ?>
                                        <div class="mb-2 flex items-center gap-2">
                                            <div><?= $right_ele['text'] ?>: </div>
                                            <select name="right_<?= $right_ele['id'] ?>" class="border rounded bg-gray-100 py-1 px-2">
                                                <option disabled selected>---</option>
                                                <?php while ($left_ele = $left->fetchArray()) { ?>
                                                    <option value="<?= $left_ele['id'] ?>"><?= $left_ele['text'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <hr class="my-5">
                        <?php } ?>
                        <div class="">
                            <button type="submit" class="py-2 px-3 border border-green-400 bg-green-400 hover:bg-green-300 rounded">Submit</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>