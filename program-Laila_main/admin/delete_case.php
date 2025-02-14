<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: /laila1/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$case_id = $_GET['id'];


$query_assignments = "DELETE FROM case_assignments WHERE case_id = ?";
$stmt_assignments = $conn->prepare($query_assignments);
$stmt_assignments->bind_param("i", $case_id);
if (!$stmt_assignments->execute()) {
    logUserAction($user_id, "فشل في حذف تعيينات القضية", "delete_case.php", "فشل");
    echo "فشل في حذف تعيينات القضية.";
    exit();
}


$query = "DELETE FROM cases WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $case_id);

if ($stmt->execute()) {
    logUserAction($user_id, "حذف القضية", "delete_case.php", "نجاح");
    header("Location: /laila1/admin");
    exit();
} else {
    logUserAction($user_id, "فشل في حذف القضية", "delete_case.php", "فشل");
    echo "فشل في حذف القضية.";
}
