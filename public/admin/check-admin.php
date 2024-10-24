<?php
session_start();
const USERNAME = "login";
const PASSWORD = "1234";
if (!isset($_SESSION['teacher']) || $_SESSION['teacher'] != USERNAME) {
    exit(header("location: /admin/login.php"));
}
$db = new SQLite3('../../db.db');
$db->enableExceptions(true);