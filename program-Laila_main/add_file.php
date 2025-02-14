<?php
include "session.php";
require_once('connection.php');
require_once('logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 2) {
  header("Location: /laila1/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;

$case_check = $conn->query("SELECT id FROM cases WHERE id = $case_id AND prosecutor_id = $user_id");
if (!$case_check || $case_check->num_rows == 0) {
  die("Unauthorized access to case.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
    $originalName = $conn->real_escape_string($_FILES['uploaded_file']['name']);
    $tempPath = $_FILES['uploaded_file']['tmp_name'];
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $uniqueFileName = time() . '_' . basename($originalName);
    $destination = $uploadDir . $uniqueFileName;

    if (move_uploaded_file($tempPath, $destination)) {

      $file_path = $conn->real_escape_string($destination);
      $insert_query = $conn->query("INSERT INTO files (file_name, file_path, case_id, prosecutor_id) VALUES ('$originalName', '$file_path', $case_id, $user_id)");
      if ($insert_query) {
        logUserAction($user_id, "تمت إضافة ملف للقضية", "add_file.php", "نجاح");
        header("Location: case_details.php?id=$case_id");
        exit();
      } else {
        logUserAction($user_id, "فشل في إضافة الملف للقضية", "add_file.php", "فشل");
        $error_message = "حدث خطأ أثناء إضافة الملف.";
      }
    } else {
      $error_message = "فشل في تحميل الملف.";
    }
  } else {
    $error_message = "الرجاء اختيار ملف للتحميل.";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>إضافة ملف</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" crossorigin="anonymous">
  <style>
    html,
    body {
      height: 100%;
    }
  </style>
</head>

<body class="bg-light h-100">
  <div class="container-fluid h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            إضافة ملف للقضية
          </div>
          <div class="card-body">
            <?php if (isset($error_message)) : ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="uploaded_file" class="form-label">اختر الملف:</label>
                <input class="form-control" type="file" id="uploaded_file" name="uploaded_file" required>
              </div>
              <button type="submit" class="btn btn-primary">رفع الملف</button>
              <a href="case_details.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">إلغاء</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>