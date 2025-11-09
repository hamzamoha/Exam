<?php
session_start();
if (isset($_SESSION['teacher'])) {
    include "../db.php";
    $teacher = $db->real_escape_string($_SESSION['teacher']);
    $teacher = $db->query("SELECT * from teachers WHERE id = '$teacher'")->fetch_assoc();
    if (!$teacher) goto Red;
} else {
    Red:
    exit(header("location: /admin/login.php"));
}
