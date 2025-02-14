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

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  header("Location: user_management.php");
  exit();
}

$user = $result->fetch_assoc();

$new_password = password_hash($user['username'], PASSWORD_DEFAULT);

$query = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $new_password, $user_id);

if ($stmt->execute()) {
  logUserAction($_SESSION['user_id'], 'reset_password', 'users', 'تم إعادة تعيين كلمة المرور للمستخدم برقم: ' . $user_id);
  header("Location: user_management.php");
  exit();
} else {
  echo "فشل في إعادة تعيين كلمة المرور.";
  logUserAction($_SESSION['user_id'], 'reset_password_failed', 'users', 'فشل في إعادة تعيين كلمة المرور للمستخدم برقم: ' . $user_id);
}
