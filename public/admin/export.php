<?php
include "check-admin.php";
$id = SQLite3::escapeString($_GET['id']);
$students = $db->query("SELECT * FROM students");
$questions = $db->query("SELECT * FROM questions WHERE exam_id = '$id' ORDER BY id ASC");
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="EXPORT_' . date("Y_m_d_H_i_s") . '.csv"');
$line = ["Student Number", "First Name", "Last Name"];
while ($question = $questions->fetchArray()) array_push($line, $question['question_text']);
$fp = fopen('php://output', 'wb');
fputcsv($fp, $line, ',');
while ($student = $students->fetchArray()) {
    $submissions = $db->query("SELECT * FROM submissions WHERE student_id = " . $student['id'] . " AND exam_id = '$id' ORDER BY question_id ASC");
    $line = [$student['student_number'], $student['first_name'], $student['last_name']];
    while ($submission = $submissions->fetchArray()) {
        array_push($line, $submission['answer']);
    }
    fputcsv($fp, $line, ',');
}
fclose($fp);
