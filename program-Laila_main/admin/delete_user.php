<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
  header("Location: /laila1/login.php");
  exit();
}

if (!isset($_GET['id'])) {
  header("Location: user_management.php");
  exit();
}

$user_id = $_GET['id'];


$delete_prosecutor_query = "DELETE FROM prosecutor WHERE id = ?";
$delete_prosecutor_stmt = $conn->prepare($delete_prosecutor_query);
$delete_prosecutor_stmt->bind_param("i", $user_id);
$delete_prosecutor_stmt->execute();


$delete_user_query = "DELETE FROM users WHERE id = ?";
$delete_user_stmt = $conn->prepare($delete_user_query);
$delete_user_stmt->bind_param("i", $user_id);

if ($delete_user_stmt->execute()) {
  logUserAction($_SESSION['user_id'], 'delete_user', 'users', 'تم حذف المستخدم برقم: ' . $user_id);
  header("Location: user_management.php");
  exit();
} else {
  echo "فشل في حذف المستخدم.";
  logUserAction($_SESSION['user_id'], 'delete_user_failed', 'users', 'فشل في حذف المستخدم برقم: ' . $user_id);
}
