<?php
session_start();

$timeout_duration = 1800;
if (isset($_SESSION['last_activity'])) {
  $session_lifetime = time() - $_SESSION['last_activity'];

  if ($session_lifetime > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: /laila1/login.php");
    exit();
  }
}

$_SESSION['last_activity'] = time();

if (isset($_SESSION['change_password']) && $_SESSION['change_password'] == true && basename($_SERVER['PHP_SELF']) != 'change_password.php') {
  header("Location: /laila1/change_password.php");
  exit();
}
