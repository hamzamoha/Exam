<?php
include "check-admin.php";
$results = $db->query('SELECT * FROM sessions JOIN (SELECT first_name, last_name, id as student_id from students) s on s.student_id = sessions.student_id JOIN (SELECT id as exam_id, title, duration_minutes FROM exams) e ON e.exam_id = sessions.exam_id LEFT JOIN (SELECT exam_id eid, student_id sid FROM submissions GROUP BY exam_id, student_id) ON sid = sessions.student_id AND eid = sessions.exam_id ORDER BY id DESC');
$sessions = [];
while ($session = $results->fetchArray()) {
    foreach ($session as $key => $value) $session[strtolower($key)] = $value;
    $sessions[$session['title']][] = $session;
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
            <div class="p-5 rounded-xl bg-white">
                <?php if (count($sessions) > 0) { ?>
                    <?php foreach ($sessions as $key => $sessions_s) { ?>
                        <h2 class="text-2xl font-bold py-1"><?= $key ?></h2>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-400 mb-5">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">First Name</th>
                                    <th class="px-6 py-3">Last Name</th>
                                    <th class="px-6 py-3">Start</th>
                                    <th class="px-6 py-3">Time</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions_s as $session) { ?>
                                    <tr class="border-b bg-gray-800 border-gray-700">
                                        <th class="px-6 py-4 font-medium whitespace-nowrap text-white"><?= $session['id'] ?></th>
                                        <td class="px-6 py-4"><?= $session['first_name'] ?></td>
                                        <td class="px-6 py-4"><?= $session['last_name'] ?></td>
                                        <td class="px-6 py-4"><?= date("H:i:s d/m/Y", $session['start_at']) ?></td>
                                        <td class="px-6 py-4" <?= (($remaining_timestamp = intval($session["END_AT"]) - time()) < 0) ? "" : "data-timestamp=\"$remaining_timestamp\"" ?>><?= (($remaining_timestamp = intval($session["END_AT"]) - time()) < 0) ? "Time's Up" : intdiv($remaining_timestamp, 3600) . ":" . intdiv($remaining_timestamp % 3600, 60) . ":" . $remaining_timestamp % 60 ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($session['eid']) { ?>
                                                <span class="text-teal-500 font-bold">Submitted</span>
                                            <?php } else { ?>
                                                <span class="text-rose-500 font-bold">Not Submitted</span>
                                                <?php if ($remaining_timestamp < 0) { ?>
                                                    <form class="inline-block" action="/admin/reset.php" method="post">
                                                        <input type="hidden" value="<?= $session['id'] ?>" name="session_id">
                                                        <input class="block cursor-pointer text-xs py-0.5 px-1 bg-blue-700 rounded text-white" type="submit" value="Delete" name="delete">
                                                    </form>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php }
                } else { ?>
                    <div class="py-14 text-4xl font-bold text-center">No Sessions Started Yet!</div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>