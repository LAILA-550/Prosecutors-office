<?php
include "session.php";
require_once('connection.php');
require_once('logger.php');

if (!isset($_SESSION['username'])) {
  header("Location: /laila1/login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $current_password = htmlspecialchars(trim($_POST['current_password']));
  $new_password = htmlspecialchars(trim($_POST['new_password']));
  $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $error_message = "الرجاء ملء جميع الحقول المطلوبة.";
  } elseif ($new_password !== $confirm_password) {
    $error_message = "كلمات المرور الجديدة غير متطابقة.";
  } else {
    // Password complexity check
    $uppercase = preg_match('@[A-Z]@', $new_password);
    $lowercase = preg_match('@[a-z]@', $new_password);
    $number    = preg_match('@[0-9]@', $new_password);
    $specialChars = preg_match('@[^\w]@', $new_password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($new_password) < 8) {
      $error_message = "يجب أن تحتوي كلمة المرور الجديدة على 8 أحرف على الأقل، وحرف كبير واحد، وحرف صغير واحد، ورقم واحد، ورمز خاص واحد.";
    } else {
      $username = $_SESSION['username'];
      $query = "SELECT password FROM users WHERE username = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $stmt->bind_result($hashed_password);
      $stmt->fetch();
      $stmt->close();

      if (!password_verify($current_password, $hashed_password)) {
        $error_message = "كلمة المرور الحالية غير صحيحة.";
      } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $new_hashed_password, $username);

        if ($stmt->execute()) {
          logUserAction($_SESSION['user_id'], 'change_password', 'users', 'تم تغيير كلمة المرور للمستخدم: ' . $username);
          unset($_SESSION['change_password']);
          header('Location: cases.php');
          exit();
        } else {
          $error_message = "فشل في تغيير كلمة المرور.";
          logUserAction($_SESSION['user_id'], 'change_password_failed', 'users', 'فشل في تغيير كلمة المرور للمستخدم: ' . $username);
        }
        $stmt->close();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تغيير كلمة المرور</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
  <style>
    html,
    body {
      height: 100%;
    }
  </style>
</head>

<body class="bg-light h-100">
  <div class="container-fluid h-100">
    <div>
      <main class="px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">تغيير كلمة المرور</h1>
        </div>
        <div class="card my-4">
          <div class="card-header">
            <h3>تغيير كلمة المرور</h3>
          </div>
          <div class="card-body">
            <?php if (isset($error_message)): ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
              </div>
            <?php elseif (isset($success_message)): ?>
              <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
              </div>
            <?php endif; ?>
            <form method="post" id="changePasswordForm">
              <div class="mb-3">
                <label for="current_password" class="form-label">كلمة المرور الحالية:</label>
                <input type="password" name="current_password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="new_password" class="form-label">كلمة المرور الجديدة:</label>
                <input type="password" name="new_password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">تأكيد كلمة المرور الجديدة:</label>
                <input type="password" name="confirm_password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>