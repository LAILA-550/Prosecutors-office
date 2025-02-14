<?php
include "session.php";
require_once('connection.php');
require_once('logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 2) {
  header("Location: /laila1/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$case_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

logUserAction($user_id, "زيارة صفحة تفاصيل القضية", "case_details.php", "نجاح");

$case = $conn->query("SELECT cases.case_number, cases.defendant, cases.description, cases.status, cases.verdict, 
                             police_departments.name AS police_department, courts.name AS court, 
                             CONCAT(prosecutor.first_name, ' ', prosecutor.second_name, ' ', prosecutor.last_name) AS prosecutor 
                      FROM cases 
                      JOIN case_assignments ON cases.id = case_assignments.case_id 
                      JOIN police_departments ON case_assignments.police_department_id = police_departments.id 
                      JOIN courts ON case_assignments.court_id = courts.id 
                      JOIN prosecutor ON cases.prosecutor_id = prosecutor.id 
                      WHERE cases.id = $case_id AND cases.prosecutor_id = $user_id");

if (!$case || $case->num_rows == 0) {
  logUserAction($user_id, "فشل في جلب تفاصيل القضية", "case_details.php", "فشل");
  die("تفاصيل القضية غير متاحة.");
}

$case_details = $case->fetch_assoc();


$notes_query = $conn->query("SELECT * FROM notes WHERE case_id = $case_id ORDER BY created_at DESC");
$notes = [];
if ($notes_query) {
  while ($row = $notes_query->fetch_assoc()) {
    $notes[] = $row;
  }
}


$files_query = $conn->query("SELECT * FROM files WHERE case_id = $case_id ORDER BY created_at DESC");
$files = [];
if ($files_query) {
  while ($row = $files_query->fetch_assoc()) {
    $files[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تفاصيل القضية</title>
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
    <div class="row h-100">

      <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">تفاصيل القضية</h1>
          <?php include '_navbar_contents.php' ?>
        </div>

        <div class="mb-3">
          <a href="cases.php" class="btn btn-secondary">العودة إلى القضايا</a>
        </div>


        <div class="card my-4">
          <div class="card-header">
            <h3>تفاصيل القضية</h3>
          </div>
          <div class="card-body">
            <p><strong>رقم القضية:</strong> <?php echo htmlspecialchars($case_details['case_number']); ?></p>
            <p><strong>المدعى عليه:</strong> <?php echo htmlspecialchars($case_details['defendant']); ?></p>
            <p><strong>الوصف:</strong> <?php echo htmlspecialchars($case_details['description']); ?></p>
            <p><strong>الحالة:</strong> <?php echo htmlspecialchars($case_details['status']); ?></p>
            <p><strong>قسم الشرطة:</strong> <?php echo htmlspecialchars($case_details['police_department']); ?></p>
            <p><strong>المحكمة:</strong> <?php echo htmlspecialchars($case_details['court']); ?></p>
            <p><strong>عضو النيابة:</strong> <?php echo htmlspecialchars($case_details['prosecutor']); ?></p>
            <?php if (!empty($case_details['verdict'])) : ?>
              <p><strong>الحكم:</strong> <?php echo htmlspecialchars($case_details['verdict']); ?></p>
            <?php endif; ?>

            <a href="change_status.php?case_id=<?php echo $case_id; ?>" class="btn btn-warning">تغيير الحالة وتحديد الحكم</a>
          </div>
        </div>


        <div class="card my-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3>ملاحظات القضية</h3>
            <a href="add_note.php?case_id=<?php echo $case_id; ?>" class="btn btn-primary">إضافة ملاحظة</a>
          </div>
          <div class="card-body">
            <?php if (count($notes) > 0) : ?>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>الملاحظة</th>
                      <th>تاريخ الإنشاء</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($notes as $note) : ?>
                      <tr>
                        <td><?php echo htmlspecialchars($note['text']); ?></td>
                        <td><?php echo htmlspecialchars($note['created_at']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <p>لا توجد ملاحظات لهذه القضية.</p>
            <?php endif; ?>
          </div>
        </div>


        <div class="card my-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3>ملفات القضية</h3>
            <a href="add_file.php?case_id=<?php echo $case_id; ?>" class="btn btn-primary">إضافة ملف</a>
          </div>
          <div class="card-body">
            <?php if (count($files) > 0) : ?>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>اسم الملف</th>
                      <th>تاريخ الإنشاء</th>
                      <th>تحميل</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($files as $file) : ?>
                      <tr>
                        <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                        <td><?php echo htmlspecialchars($file['created_at']); ?></td>
                        <td>

                          <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download>تحميل</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <p>لا توجد ملفات لهذه القضية.</p>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>