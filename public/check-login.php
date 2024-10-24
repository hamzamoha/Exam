<?php
session_start();
$db = new SQLite3('../db.db');
if (!isset($_SESSION['student'])) exit(header('location: /'));
else {
    $res = $db->query("SELECT * FROM students WHERE student_number = '" . SQLite3::escapeString($_SESSION['student']) . "'");
    if (!($student = $res->fetchArray())) exit(header('location: /'));
}
