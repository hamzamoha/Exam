<?php
include "check-admin.php";
$id = SQLite3::escapeString($_GET['id']);
$exam = $db->query("SELECT * FROM exams WHERE id = '$id'")->fetchArray();
if (!$exam) {
    header("location: /admin/submissions.php");
}
header('Content-Type: text/csv; charset=utf-8');
$fp = fopen('php://output', 'wb');
if (isset($_GET['type']) && $_GET['type'] == 'grade') {
    header('Content-Disposition: attachment; filename="EXAM_GRADES_' . date("Y_m_d_H_i_s") . '.csv"');
    $line = ["Student Number", "First Name", "Last Name", "Grade"];
    fputcsv($fp, $line, ',');
    $results = $db->query("SELECT student_number, first_name, last_name, s FROM students LEFT JOIN (SELECT exam_id, student_id, count(*) as p, sum(score) as s FROM submissions WHERE exam_id = '$id' GROUP BY student_id, exam_id) ON id = student_id");
    while ($line = $results->fetchArray()) {
        $line = [$line['student_number'], $line['first_name'], $line['last_name'], $line['s']];
        fputcsv($fp, $line, ',');
    }
} else {
    $students = $db->query("SELECT * FROM students");
    $questions = $db->query("SELECT * FROM questions WHERE exam_id = '$id' ORDER BY id ASC");
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
}
fclose($fp);
