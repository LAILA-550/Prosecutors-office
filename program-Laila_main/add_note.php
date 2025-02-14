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
  $note_text = isset($_POST['note_text']) ? trim($_POST['note_text']) : '';

  if (!empty($note_text)) {
    $insert_query = $conn->query("INSERT INTO notes (text, case_id, prosecutor_id) VALUES ('$note_text', $case_id, $user_id)");

    if ($insert_query) {
      logUserAction($user_id, "تمت إضافة ملاحظة للقضية", "add_note.php", "نجاح");
      header("Location: case_details.php?id=$case_id");
      exit();
    } else {
      logUserAction($user_id, "فشل في إضافة ملاحظة للقضية", "add_note.php", "فشل");
      $error_message = "حدث خطأ أثناء إضافة الملاحظة.";
    }
  } else {
    $error_message = "الرجاء إدخال نص الملاحظة.";
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>إضافة ملاحظة</title>
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
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            إضافة ملاحظة للقضية
          </div>
          <div class="card-body">
            <?php if (isset($error_message)) : ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label for="note_text" class="form-label">نص الملاحظة:</label>
                <textarea class="form-control" id="note_text" name="note_text" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">حفظ الملاحظة</button>
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