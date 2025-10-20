<?php
session_start();
$err = 0;
$db = new SQLite3('../db.db');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = SQLite3::escapeString($_POST['username']);
        $password = SQLite3::escapeString($_POST['password']);
        $result = $db->query("SELECT student_number, password FROM students WHERE student_number = '$username'");
        if ($user = $result->fetchArray()) {
            if ($user['password'] == $password) {
                $_SESSION['student'] = $user['student_number'];
            } else $err = 2; // pass
        } else $err = 1; // user
    }
    //exit(header("location: /"));
}
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة الاختبارات</title>
    <link rel="stylesheet" href="/style.css">
    <?php
    if (isset($_SESSION['student'])) {
    ?>
        <style>
            body {
                background: #fafafa;
            }
            </style>
    <?php
    } else {
        ?>
        <style>
            body {
                background: linear-gradient(133.64deg, #5c61a3, #105ba6 100%, #09203f 0);
            }
        </style>
    <?php
    } ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <script>
        if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <?php
    if (isset($_SESSION['student'])) {
        $res = $db->query("SELECT * FROM students WHERE student_number = '" . SQLite3::escapeString($_SESSION['student']) . "'");
        if ($student = $res->fetchArray()) {
            $student_id = $student['id'];
            $exams_topass = $db->query('SELECT exams.*, class FROM exams LEFT JOIN exams_class ON exams.id = exam_id WHERE visible = 1 AND exam_id NOT IN (SELECT DISTINCT exam_id FROM submissions WHERE student_id = ' . $student['id'] . ') AND class = \'' . $student['class'] . '\'');
            $exams_passed = $db->query("SELECT * FROM (SELECT exam_id, sum(score) s, sum(points) p FROM (SELECT question_id, score FROM submissions WHERE student_id = '$student_id') sub JOIN (SELECT id, exam_id, points FROM questions) qst ON question_id = id GROUP BY exam_id) JOIN exams ON exams.id = exam_id  WHERE exams.id IN (SELECT DISTINCT exam_id FROM submissions WHERE student_id = '$student_id') AND exams.id in (SELECT distinct exam_id FROM exams_class WHERE class = '$student[class]')");
            include "topnav.php"; ?>
            <div class="p-5">
                <div class="bg-white border p-8 shadow-lg">
                    <h2 class="text-4xl mb-5">الاختبارات</h2>
                    <div class="grid grid-cols-4 gap-8">
                        <?php while ($exam = $exams_topass->fetchArray()) { 
                            $subject = $db->query("SELECT subject FROM teachers WHERE id = '$exam[teacher_id]'")->fetchArray()['subject'] ?>
                            <div class="border bg-slate-100 rounded p-3 border-slate-300 shadow">
                                <h2 class="py-1 text-xl font-bold"><?= $exam['title'] ?></h2>
                                <h3 class="font-semibold text-gray-500"><?= $subject ?></h3>
                                <div class="py-1 text-slate-500"><?= date("d/m/Y", strtotime($exam['created_at'])) ?></div>
                                <div class="text-right">
                                    <a class="bg-indigo-600 py-1 px-2 inline-block text-white rounded hover:bg-indigo-500 transition-all" href="pass-exam.php?id=<?= $exam['id'] ?>">بدأ التمرير</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="bg-white border p-8 shadow-lg">
                    <h2 class="text-4xl mb-5">النتائج</h2>
                    <div class="grid grid-cols-4 gap-8">
                        <?php while ($exam = $exams_passed->fetchArray()) {
                            $subject = $db->query("SELECT subject FROM teachers WHERE id = '$exam[teacher_id]'")->fetchArray()['subject'] ?>
                            <div class="border bg-slate-100 rounded p-3 border-slate-300 shadow">
                                <h2 class="py-1 text-xl font-bold"><?= $exam['title'] ?></h2>
                                <h3 class="font-semibold text-gray-500"><?= $subject ?></h3>
                                <div class="py-1 text-slate-500"><?= date("d/m/Y", strtotime($exam['created_at'])) ?></div>
                                <div>
                                    <?php if ($exam['graded'] == 1) { ?>
                                        <span class="py-1">النتيجة: <?= $exam['s'] . "/" . $exam['p'] ?></span>
                                    <?php } ?>
                                </div>
                                <div class="text-right">
                                    <?php if ($exam['graded'] == 1) { ?>
                                        <a class="bg-teal-600 py-1 px-2 inline-block text-white rounded hover:bg-teal-500 transition-all" href="resultat.php?id=<?= $exam['id'] ?>">التصحيح</a>
                                    <?php } else { ?>
                                        <span class="bg-slate-600 py-1 px-2 inline-block text-white rounded">ليس بعد</span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else goto a;
    } else {
        a: ?>
        <div class="p-5">
            <h1 class="text-center p-5 text-7xl text-white mb-6">منصة الاختبارات</h1>
            <div class="p-10 bg-white">
                <h3 class="text-center text-2xl">ثانوية الرازي الإعدادية</h3>
                <div>
                    <form action="?" method="post" class="">
                        <div class="my-6 max-w-full w-80 mx-auto">
                            <div class="font-bold py-1 text-gray-500">رمز مسار</div>
                            <input class="block w-full border bg-slate-100 rounded-lg p-3 outline-none" type="text" name="username" placeholder="رمز مسار" id="username" value="<?= $err == 2 ? htmlspecialchars($_POST['username']) : "" ?>">
                        </div>
                        <div class="my-6 max-w-full w-80 mx-auto">
                            <div class="font-bold py-1 text-gray-500">كلمة المرور</div>
                            <input class="block w-full border bg-slate-100 rounded-lg p-3 outline-none" type="password" name="password" placeholder="كلمة المرور" id="password">
                        </div>
                        <input class="block my-5 mx-auto max-w-full w-80 bg-[#ff8723] px-2 py-3 hover:bg-[#ffaf28] cursor-pointer rounded text-white transition-all text-sm font-bold" type="submit" value="تسجيل الدخول" name="login">
                        <?php if ($err > 0) { ?>
                            <p class="text-red-500 text-center py-2">
                                <?= $err == 2 ? "كلمة المرور خاطئة" : "رمز مسار خاطئ" ?>
                            </p>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    <?php }
    ?>

</body>

</html>