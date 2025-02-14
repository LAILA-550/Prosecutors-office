<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
  header("Location: /laila1/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

logUserAction($user_id, 'visit', 'courts', 'Visited courts page');


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_court'])) {
  $name = $_POST['name'];
  $location = $_POST['location'];

  $query = "INSERT INTO courts (name, location) VALUES (?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $name, $location);

  if ($stmt->execute()) {
    logUserAction($user_id, 'add', 'courts', 'Added court: ' . $name);
  } else {
    echo "فشل في إضافة المحكمة.";
  }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_court'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $location = $_POST['location'];

  $query = "UPDATE courts SET name = ?, location = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ssi", $name, $location, $id);

  if ($stmt->execute()) {
    logUserAction($user_id, 'edit', 'courts', 'Edited court: ' . $name);
  } else {
    echo "فشل في تعديل المحكمة.";
  }
}


if (isset($_GET['delete_id'])) {
  $id = $_GET['delete_id'];

  $query = "DELETE FROM courts WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    logUserAction($user_id, 'delete', 'courts', 'Deleted court with ID: ' . $id);
  } else {
    echo "فشل في حذف المحكمة.";
  }
}


$query = "SELECT * FROM courts";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>إدارة المحاكم</title>
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
      <?php include('_sidebar.php'); ?>


      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">إدارة المحاكم</h1>
          <?php include '../_navbar_contents.php' ?>
        </div>

        <div class="card my-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3>المحاكم</h3>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourtModal">
              إضافة محكمة جديدة
            </button>
          </div>
          <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">الاسم</th>
                      <th scope="col">الموقع</th>
                      <th scope="col">الإجراءات</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($court = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($court['id']); ?></td>
                        <td><?php echo htmlspecialchars($court['name']); ?></td>
                        <td><?php echo htmlspecialchars($court['location']); ?></td>
                        <td>

                          <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCourtModal<?php echo $court['id']; ?>">
                            تعديل
                          </button>
                        </td>
                      </tr>


                      <div class="modal fade" id="editCourtModal<?php echo $court['id']; ?>" tabindex="-1" aria-labelledby="editCourtModalLabel<?php echo $court['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editCourtModalLabel<?php echo $court['id']; ?>">تعديل محكمة</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <form method="post">
                                <input type="hidden" name="edit_court" value="1">
                                <input type="hidden" name="id" value="<?php echo $court['id']; ?>">
                                <div class="mb-3">
                                  <label for="name" class="form-label">الاسم:</label>
                                  <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($court['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                  <label for="location" class="form-label">الموقع:</label>
                                  <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($court['location']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">لا توجد محاكم.</p>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </div>
  </div>


  <div class="modal fade" id="addCourtModal" tabindex="-1" aria-labelledby="addCourtModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCourtModalLabel">إضافة محكمة جديدة</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post">
            <input type="hidden" name="add_court" value="1">
            <div class="mb-3">
              <label for="name" class="form-label">الاسم:</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="location" class="form-label">الموقع:</label>
              <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <button type="submit" class="btn btn-primary">إضافة</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>