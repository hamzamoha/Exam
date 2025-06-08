<?php
session_start();
$db = new SQLite3('../../db.db');
$db->enableExceptions(true);
if (isset($_SESSION['teacher'])) {
    $teacher = SQLite3::escapeString($_SESSION['teacher']);
    $teacher = $db->query("SELECT * from teachers WHERE id = '$teacher'")->fetchArray();
    if (!$teacher) goto Red;
} else {
    Red:
    exit(header("location: /admin/login.php"));
}
