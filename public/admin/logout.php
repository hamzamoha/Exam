<?php
include "check-admin.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    unset($_SESSION['teacher']);
exit(header("location: /admin/login.php"));
