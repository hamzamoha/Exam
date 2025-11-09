<?php
session_start();
include "db.php";
if (!isset($_SESSION['student'])) exit(header('location: /'));
$sql = "SELECT * FROM students WHERE student_number = '" . $db->real_escape_string($_SESSION['student']) . "'";
$res = $db->query($sql);
if (!($student = $res->fetch_assoc())) exit(header('location: /'));
