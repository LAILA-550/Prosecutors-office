<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: /laila1/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $case_id = $_POST['id'];
    $status = $_POST['status'];


    if ($status == 'متأخر') {
        $message = "القضية رقم $case_id متأخرة. يرجى اتخاذ الإجراءات اللازمة.";
        $prosecutor_id = $_SESSION['user_id'];


        $query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $prosecutor_id, $message);
        $stmt->execute();
    }


    $query = "UPDATE cases SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $case_id);
    if ($stmt->execute()) {
        logUserAction($_SESSION['user_id'], "تعديل القضية", "edit_case.php", "نجاح");
        header("Location: /laila1/admin");
        exit();
    } else {
        logUserAction($_SESSION['user_id'], "فشل في تعديل القضية", "edit_case.php", "فشل");
        echo "فشل في تعديل القضية.";
    }
}
