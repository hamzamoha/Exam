<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    if (isset($_POST['logout'])) {
        unset($_SESSION['student']);
        header("location: /");
    }
