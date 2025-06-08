<?php
include "check-admin.php";
$exam_id = SQLite3::escapeString($_GET['id']);
$exam = $db->query("SELECT * FROM exams WHERE id = '$exam_id'")->fetchArray();
if (!$exam || $exam['teacher_id'] != $teacher['id']) exit(header("location: /admin/exams.php"));
$teacher_classes = $db->query("SELECT * FROM teachers_class WHERE teacher_id = '" . $teacher['id'] . "'");
$exam_classes1 = $db->query("SELECT * FROM exams_class WHERE exam_id = '$exam_id'");
$exam_classes = [];
while ($class = $exam_classes1->fetchArray()) $exam_classes[] = $class['class'];
unset($exam_classes1);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = SQLite3::escapeString($_POST['title']);
    $description = SQLite3::escapeString($_POST['description']);
    $duration_minutes = SQLite3::escapeString($_POST['duration']);
    $db->exec("DELETE FROM exams_class WHERE exam_id = '$exam_id'");
    $classes = $_POST['class'];
    foreach ($classes as $class) {
        $class = SQLite3::escapeString($class);
        $db->exec("INSERT INTO exams_class (exam_id, class) SELECT '$exam_id', class FROM teachers_class WHERE class = '$class' AND teacher_id = '" . $teacher['id'] . "'");
    }
    $db->exec("UPDATE exams SET title = '$title', description = '$description', duration_minutes = '$duration_minutes' WHERE id = '$exam_id'");
    exit(header("location: /admin/view.php?id=$exam_id"));
}
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
            <form class="bg-white p-5 rounded-xl" action="?id=<?= $exam_id ?>" method="post">
                <h2 class="text-3xl mb-2">Modify Exam: <?= $exam['title'] ?></h2>
                <hr class="my-5">
                <div class="mb-2">
                    <div class="grid grid-cols-2 gap-2 my-6">
                        <div>
                            <div class="font-bold py-1 text-gray-500">Title</div>
                            <input name="title" type="text" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" value="<?= htmlspecialchars($exam['title']) ?>" required>
                        </div>
                        <div>
                            <div class="font-bold py-1 text-gray-500">Duration (in minutes)</div>
                            <input name="duration" type="number" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" value="<?= htmlspecialchars($exam['duration_minutes']) ?>" required>
                        </div>
                    </div>
                    <div class="my-6">
                        <div class="font-bold py-1 text-gray-500">Description</div>
                        <input name="description" type="text" class="block w-full font-bold bg-slate-100 rounded-lg p-3 outline-none" value="<?= htmlspecialchars($exam['description']) ?>">
                    </div>
                    <div class="my-6">
                        <div class="font-bold py-1 text-gray-500">Classes</div>
                        <div class="flex py-2 gap-5 flex-wrap">
                            <?php while ($class = $teacher_classes->fetchArray()) { ?>
                                <label for="class_<?= $class['class'] ?>" class="select-none block p-2 rounded cursor-pointer bg-slate-100 has-[:checked]:bg-emerald-400 has-[:checked]:text-white"><?= $class['class'] ?><input<?= in_array($class['class'], $exam_classes) ? " checked" : "" ?> type="checkbox" class="hidden" name="class[]" id="class_<?= $class['class'] ?>" value="<?= htmlspecialchars($class['class']) ?>"></label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="rounded bg-indigo-600 py-2 px-4 hover:bg-indigo-500 text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>