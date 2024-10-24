<?php
include "check-admin.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['question_text'])) {
        $exam_id = SQLite3::escapeString($_GET['id']);
        $type = SQLite3::escapeString($_POST['type']);
        $question_text = SQLite3::escapeString($_POST['question_text']);
        $options = $correct_answer = null;
        switch ($type) {
            case 'mcq':
                $options = json_encode([
                    'options' => [SQLite3::escapeString($_POST['choice_1']), SQLite3::escapeString($_POST['choice_2']), SQLite3::escapeString($_POST['choice_3']), SQLite3::escapeString($_POST['choice_4'])]
                ]);
                $correct_answer = $_POST[$_POST['correct_choice']] ? SQLite3::escapeString($_POST[$_POST['correct_choice']]) : null;
                break;
            case 'true_false':
                $correct_answer = $_POST["true_flase"] ? SQLite3::escapeString($_POST["true_flase"]) : null;
                break;
            case 'short_answer':
                $correct_answer = $_POST["correct_answer"] ? SQLite3::escapeString($_POST["correct_answer"]) : null;
                break;
            case 'matching_pairs':
                // just in case
                break;
        }
        $points = floatval($_POST['points']) ? SQLite3::escapeString(floatval($_POST['points'])) : 1;
        $db->exec("INSERT INTO questions (exam_id, type, question_text, options, correct_answer, points) VALUES ('$exam_id', '$type', '$question_text', '$options', '$correct_answer', '$points')");
        if ($type == "matching_pairs") {
            $question_id = $db->lastInsertRowID();
            foreach ($_POST as $key => $value) {
                if (preg_match("/^left_\d+$/i", $key)) {
                    $index = substr($key, 5);
                    $text = SQLite3::escapeString(trim($value));
                    $db->exec("INSERT INTO matching_pairs(question_id, text, parent_id) VALUES ($question_id, '$text', 0)");
                    $parent_id = $db->lastInsertRowID();
                    $text = SQLite3::escapeString(trim($_POST["right_$index"]));
                    $db->exec("INSERT INTO matching_pairs(question_id, text, parent_id) VALUES ($question_id, '$text', $parent_id)");
                }
            }
        }
        header("location: /admin/edit.php?id=" . $_GET['id']);
    }
    if (isset($_POST['delete'])) {
        if (isset($_POST['question_id'])) {
            $id = SQLite3::escapeString($_POST['question_id']);
            $db->exec("DELETE FROM questions WHERE id = '$id'");
            header("location: /admin/edit.php?id=" . $_GET['id']);
        }
    }
}
$results = $db->query('SELECT * FROM exams WHERE id = ' . $_GET['id']);
$exam = $results->fetchArray();
$questions = $db->query('SELECT * FROM questions WHERE exam_id = ' . $_GET['id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.question_form.type.forEach(e => {
                e.addEventListener("change", () => {
                    document.querySelectorAll("[data-question-type]").forEach(t => {
                        t.classList.add("hidden")
                    })
                    document.querySelector("[data-question-type='" + e.value + "']").classList.remove("hidden")
                })
            });
        })
    </script>
</head>


<body class="bg-slate-100">
    <?php include "topnav.php"; ?>
    <div class="flex gap-10 p-10">
        <div>
            <?php include "sidenav.php"; ?>
        </div>
        <div class="flex-1">
            <div class="bg-white p-5 rounded-xl">
                <h1 class="text-3xl font-bold mb-2"><?= $exam['title'] ?></h1>
                <?php
                while ($question = $questions->fetchArray()) { ?>
                    <div class="p-2 my-4 rounded border relative">
                        <form class="block absolute top-2 right-2" action="?id=<?= $_GET['id'] ?>" method="post">
                            <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                            <button type="submit" value="Delete" name="delete" class="text-white bg-red-500 cursor-pointer w-5 h-5 flex items-center justify-center text-center rounded">x</button>
                        </form>
                        <h3 class="text-lg font-bold mb-2">
                            <?= $question['question_text'] ?> (<?= $question['points'] ?>p)
                            <?php if ($question['type'] == 'mcq') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-amber-500">Multiple Choice</div>
                            <?php } else if ($question['type'] == 'true_false') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-emerald-500">True or False</div>
                            <?php } else if ($question['type'] == 'short_answer') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-rose-500">Short Answer</div>
                            <?php } else if ($question['type'] == 'matching_pairs') { ?>
                                <div class="inline-block text-xs px-1 py-0.5 rounded bg-sky-500">Matching Pairs</div>
                            <?php } ?>
                        </h3>
                        <?php if ($question['correct_answer']) { ?>
                            <div>
                                <b>Correct Answer:</b> <?= $question['correct_answer'] ?>
                            </div>
                        <?php } ?>
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
                <hr class="my-5">
                <form name="question_form" action="?id=<?= $_GET['id'] ?>" method="post">
                    <h2 class="py-0.5 text-2xl">Add Question</h2>
                    <div>
                        <div class="flex gap-2 items-center py-1 mb-5">
                            <label for="question_text">Question Text</label><input class="flex-1 font-bold bg-slate-100 rounded-lg p-3 outline-none" type="text" name="question_text" placeholder="Question Text" id="question_text" required>
                            <label for="points">Points</label><input class="w-20 font-bold bg-slate-100 rounded-lg p-3 outline-none" value="1" type="number" min="0" max="20" step="0.25" name="points" placeholder="Points" id="points" required>
                        </div>
                        <div class="flex gap-5 justify-center mb-5">
                            <div>
                                <input class="peer hidden type_hidden" type="radio" id="type_mcq" name="type" value="mcq" required>
                                <label class="rounded-full bg-indigo-400 px-4 py-3 cursor-pointer hover:bg-indigo-300 peer-checked:bg-indigo-600 text-white select-none" for="type_mcq">Multiple Choice</label>
                            </div>
                            <div>
                                <input class="peer hidden type_hidden" type="radio" id="type_true_false" name="type" value="true_false" required>
                                <label class="rounded-full bg-indigo-400 px-4 py-3 cursor-pointer hover:bg-indigo-300 peer-checked:bg-indigo-600 text-white select-none" for="type_true_false">True or False</label>
                            </div>
                            <div>
                                <input class="peer hidden type_hidden" type="radio" id="type_short_answer" name="type" value="short_answer" required>
                                <label class="rounded-full bg-indigo-400 px-4 py-3 cursor-pointer hover:bg-indigo-300 peer-checked:bg-indigo-600 text-white select-none" for="type_short_answer">Short Answer</label>
                            </div>
                            <div>
                                <input class="peer hidden type_hidden" type="radio" id="type_matching_pairs" name="type" value="matching_pairs" required>
                                <label class="rounded-full bg-indigo-400 px-4 py-3 cursor-pointer hover:bg-indigo-300 peer-checked:bg-indigo-600 text-white select-none" for="type_matching_pairs">Matching Pairs</label>
                            </div>
                        </div>
                        <div class="hidden" data-question-type="mcq">
                            <div class="flex gap-2 items-center my-5">
                                <label for="choice_1" class="whitespace-nowrap">Choice 1 </label>
                                <input class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" type="text" name="choice_1" id="choice_1" placeholder="Choice 1">
                                <div class="text-center whitespace-nowrap">
                                    <input type="radio" name="correct_choice" class="cursor-pointer accent-green-600 outline-none w-6 h-6" id="correct_choice_1" value="choice_1">
                                </div>
                            </div>
                            <div class="flex gap-2 items-center my-5">
                                <label for="choice_2" class="whitespace-nowrap">Choice 2 </label>
                                <input class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" type="text" name="choice_2" id="choice_2" placeholder="Choice 2">
                                <div class="text-center whitespace-nowrap">
                                    <input type="radio" name="correct_choice" class="cursor-pointer accent-green-600 outline-none w-6 h-6" id="correct_choice_2" value="choice_2">
                                </div>
                            </div>
                            <div class="flex gap-2 items-center my-5">
                                <label for="choice_3" class="whitespace-nowrap">Choice 3 </label>
                                <input class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" type="text" name="choice_3" id="choice_3" placeholder="Choice 3">
                                <div class="text-center whitespace-nowrap">
                                    <input type="radio" name="correct_choice" class="cursor-pointer accent-green-600 outline-none w-6 h-6" id="correct_choice_3" value="choice_3">
                                </div>
                            </div>
                            <div class="flex gap-2 items-center my-5">
                                <label for="choice_4" class="whitespace-nowrap">Choice 4 </label>
                                <input class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" type="text" name="choice_4" id="choice_4" placeholder="Choice 4">
                                <div class="text-center whitespace-nowrap">
                                    <input type="radio" name="correct_choice" class="cursor-pointer accent-green-600 outline-none w-6 h-6" id="correct_choice_4" value="choice_4">
                                </div>
                            </div>
                        </div>
                        <div class="hidden" data-question-type="true_false">
                            <div class="flex gap-1 justify-center items-center mb-2">
                                <label for="true_t">True</label>
                                <input type="radio" name="true_flase" id="true_t" value="true">
                                <input type="radio" name="true_flase" id="false_f" value="false">
                                <label for="false_f">False</label>
                            </div>
                        </div>
                        <div class="hidden" data-question-type="short_answer">
                            <div class="flex gap-1 justify-center items-center mb-2">
                                <label for="correct_answer">Correct Answer</label>
                                <input type="text" name="correct_answer" id="correct_answer" placeholder="Correct Answer" class="bg-neutral-100 border border-neutral-200 shadow-xs py-1 px-0.5">
                            </div>
                        </div>
                        <div class="hidden" data-question-type="matching_pairs">
                            <table class="max-w-ful w-[550px] mx-auto mb-2 border-collapse border border-slate-500">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1 border border-slate-600">Left</th>
                                        <th class="px-2 py-1 border border-slate-600">Right</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="left_1" placeholder="Left...">
                                        </td>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="right_1" placeholder="Right...">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="left_2" placeholder="Left...">
                                        </td>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="right_2" placeholder="Right...">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="left_3" placeholder="Left...">
                                        </td>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="right_3" placeholder="Right...">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="left_4" placeholder="Left...">
                                        </td>
                                        <td class="border border-slate-600">
                                            <input type="text" class="bg-white px-3 py-2 w-full h-full outline-none" name="right_4" placeholder="Right...">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="rounded bg-sky-400 py-1 px-2">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</body>

</html>