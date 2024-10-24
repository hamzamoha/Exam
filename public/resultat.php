<?php
include "check-login.php";
$exam_id = SQLite3::escapeString($_GET['id']);
$student_id = $student['id'];
$results = $db->query("SELECT * FROM (SELECT exam_id, sum(score) s, sum(points) p FROM (SELECT question_id, score FROM submissions WHERE student_id = '$student_id') sub JOIN (SELECT id, exam_id, points FROM questions) qst ON question_id = id GROUP BY exam_id) JOIN exams ON exams.id = exam_id  WHERE exams.id = '$exam_id' AND exams.graded = 1 AND exams.id IN (SELECT DISTINCT exam_id FROM submissions WHERE student_id = '$student_id')");
if (!($exam = $results->fetchArray())) header('location: /');
$questions = $db->query("SELECT * FROM questions JOIN (SELECT question_id, student_id, score, answer FROM submissions) ON questions.id = question_id WHERE questions.exam_id = '$exam_id' AND student_id = '$student_id'");
$to_connect = [
    "correct" => [],
    "answer" => []
];
while ($question = $questions->fetchArray()) {
    if ($question['type'] == 'matching_pairs') {
        $right = $db->query("SELECT * FROM matching_pairs WHERE parent_id > 0 AND question_id = " . $question['id'] . " ORDER BY RANDOM()");
        while ($ele = $right->fetchArray()) {
            $to_connect["correct"][$ele['id']] = $ele['parent_id'];
        }
        foreach (json_decode($question['answer']) as $key => $value) {
            $to_connect["answer"][$key] = $value;
        }
    }
}
$to_connect = json_encode($to_connect);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body {
            background: url('/imgs/wall.jpg');
        }
    </style>
    <script>
        function getOffset(el) {
            var rect = el.getBoundingClientRect();
            return {
                left: rect.left + window.pageXOffset,
                top: rect.top + window.pageYOffset,
                width: rect.width || el.offsetWidth,
                height: rect.height || el.offsetHeight
            };
        }

        function connect(div1, div2, color, thickness) { // draw a line connecting elements
            var off1 = getOffset(div1);
            var off2 = getOffset(div2);
            // bottom right
            var x1 = off1.left;
            var y1 = off1.top + (off1.height / 2);
            // top right
            var x2 = off2.left + off2.width;
            var y2 = off2.top + (off2.height / 2);
            // distance
            var length = Math.sqrt(((x2 - x1) * (x2 - x1)) + ((y2 - y1) * (y2 - y1)));
            // center
            var cx = ((x1 + x2) / 2) - (length / 2);
            var cy = ((y1 + y2) / 2) - (thickness / 2);
            // angle
            var angle = Math.atan2((y1 - y2), (x1 - x2)) * (180 / Math.PI);
            // make hr
            var htmlLine = "<div style='padding:0px; margin:0px; height:" + thickness + "px; background-color:" + color + "; line-height:1px; position:absolute; left:" + cx + "px; top:" + cy + "px; width:" + length + "px; -moz-transform:rotate(" + angle + "deg); -webkit-transform:rotate(" + angle + "deg); -o-transform:rotate(" + angle + "deg); -ms-transform:rotate(" + angle + "deg); transform:rotate(" + angle + "deg);' />";
            //
            // alert(htmlLine);
            document.body.innerHTML += htmlLine;
        }

        let to_connect = JSON.parse("<?= addslashes($to_connect) ?>");
        document.addEventListener("DOMContentLoaded", function() {
            Object.keys(to_connect.answer).forEach((key) => {
                if (to_connect.correct[key] == to_connect.answer[key]) connect(document.querySelector(`[data-id="${key}"]`), document.querySelector(`[data-id="${to_connect.answer[key]}"]`), "#0F0", 3);
                else connect(document.querySelector(`[data-id="${key}"]`), document.querySelector(`[data-id="${to_connect.answer[key]}"]`), "#F00", 3);
            });
        });
    </script>
</head>

<body class="bg-sky-200">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div class="w-52 h-52 bg-white relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rotate-45 w-1 h-72 bg-black"></div>
            <div class="absolute text-7xl -translate-x-1/2 -translate-y-1/2 top-1/4 left-1/4"><?= $exam['s'] ?></div>
            <div class="absolute text-7xl translate-x-1/2 translate-y-1/2 bottom-1/4 right-1/4"><?= $exam['p'] ?></div>
        </div>
        <div class="flex-1">
            <div class="bg-white/95 rounded-xl p-10">
                <h1 class="text-3xl font-bold mb-2"><?= $exam['title'] ?></h1>
                <hr class="my-5">
                <?php $c = 1;
                while ($question = $questions->fetchArray()) { ?>
                    <div class="py-2">
                        <h2 class="text-xl mb-2"><b>Question <?= $c++ ?>: </b><?= $question['question_text'] ?> (<?= $question['points'] ?>p)</h2>
                        <div class="flex flex-col gap-3 py-1.5">
                            <?php if ($question['type'] == 'short_answer') {
                                if ($question['score'] == 0) { ?>
                                    <div class="text-red-500 line-through"><?= $question['answer'] ?></div>
                                <?php } else if (floatval($question['score']) > 0 && floatval($question['score']) < floatval($question['points'])) { ?>
                                    <div class="text-amber-500"><?= $question['answer'] ?> <span class="text-black">(<?= $question['score'] ?>p)</span></div>
                                <?php }
                            } else if ($question['type'] == 'matching_pairs') {
                                $left = $db->query("SELECT * FROM matching_pairs WHERE parent_id = 0 AND question_id = " . $question['id'] . " ORDER BY RANDOM()");
                                $right = $db->query("SELECT * FROM matching_pairs WHERE parent_id > 0 AND question_id = " . $question['id'] . " ORDER BY RANDOM()");
                                ?>
                                <div><?= floatval($question['score']) < floatval($question['points']) ? $question['score'] . 'p' : "" ?></div>
                                <div class="flex gap-10">
                                    <div>
                                        <?php
                                        while ($ele = $left->fetchArray()) { ?>
                                            <div>
                                                <div class="my-1 px-2 py-1 border rounded" data-id="<?= $ele['id'] ?>"><?= $ele['text'] ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?php
                                        while ($ele = $right->fetchArray()) { ?>
                                            <div>
                                                <div class="my-1 px-2 py-1 border rounded" data-id="<?= $ele['id'] ?>"><?= $ele['text'] ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } else if (strtolower($question['correct_answer']) != strtolower($question['answer'])) { ?>
                                <div class="text-red-500 line-through"><?= $question['answer'] ?></div>
                            <?php } ?>
                            <div class="text-green-500"><?= $question['correct_answer'] ?></div>
                        </div>
                    </div>
                    <hr class="my-5">
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>