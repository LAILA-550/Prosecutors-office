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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $status = $_POST['status'];
  $verdict = $_POST['verdict'];

  $stmt = $conn->prepare("UPDATE cases SET status = ?, verdict = ? WHERE id = ? AND prosecutor_id = ?");
  $stmt->bind_param('ssii', $status, $verdict, $case_id, $user_id);

  if ($stmt->execute()) {
    logUserAction($user_id, "تحديث حالة القضية وتحديد الحكم", "change_status.php", "نجاح");
    header("Location: case_details.php?id=$case_id");
    exit();
  } else {
    logUserAction($user_id, "فشل في تحديث حالة القضية وتحديد الحكم", "change_status.php", "فشل");
    $error_message = "فشل في تحديث حالة القضية وتحديد الحكم.";
  }
}


$case = $conn->query("SELECT status, verdict FROM cases WHERE id = $case_id AND prosecutor_id = $user_id");
if (!$case || $case->num_rows == 0) {
  die("تفاصيل القضية غير متاحة.");
}

$case_details = $case->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تغيير الحالة وتحديد الحكم</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
</head>

<body class="bg-light h-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card my-4">
          <div class="card-header">
            <h3>تغيير الحالة وتحديد الحكم</h3>
          </div>
          <div class="card-body">
            <?php if (isset($error_message)) : ?>
              <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label for="status" class="form-label">الحالة</label>
                <select name="status" class="form-select" required>
                  <option value="قيد التحقيق" <?php echo $case_details['status'] == 'قيد التحقيق' ? 'selected' : ''; ?>>قيد التحقيق</option>
                  <option value="مغلق" <?php echo $case_details['status'] == 'مغلق' ? 'selected' : ''; ?>>مغلق</option>
                  <option value="مفتوح" <?php echo $case_details['status'] == 'مفتوح' ? 'selected' : ''; ?>>مفتوح</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="verdict" class="form-label">الحكم</label>
                <textarea class="form-control" id="verdict" name="verdict" rows="4" required><?php echo htmlspecialchars($case_details['verdict']); ?></textarea>
              </div>
              <button type="submit" class="btn btn-primary">تحديث</button>
              <a href="case_details.php?id=<?php echo $case_id; ?>" class="btn btn-secondary">إلغاء</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>