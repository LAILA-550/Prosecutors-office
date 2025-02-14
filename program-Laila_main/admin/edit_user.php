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
$query = "SELECT users.*, prosecutor.first_name, prosecutor.second_name, prosecutor.last_name 
          FROM users 
          LEFT JOIN prosecutor ON users.id = prosecutor.id 
          WHERE users.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  header("Location: user_management.php");
  exit();
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $first_name = $_POST['first_name'];
  $second_name = $_POST['second_name'];
  $last_name = $_POST['last_name'];
  $role_id = $_POST['role_id'];

  $update_query = "UPDATE users SET username = ?, role_id = ? WHERE id = ?";
  $update_stmt = $conn->prepare($update_query);
  $update_stmt->bind_param("sii", $username, $role_id, $user_id);

  if ($update_stmt->execute()) {
    if ($role_id == 2) {
      $prosecutor_query = "REPLACE INTO prosecutor (id, first_name, second_name, last_name) VALUES (?, ?, ?, ?)";
      $prosecutor_stmt = $conn->prepare($prosecutor_query);
      $prosecutor_stmt->bind_param("isss", $user_id, $first_name, $second_name, $last_name);
      $prosecutor_stmt->execute();
    }
    logUserAction($_SESSION['user_id'], 'edit_user', 'users', 'تم تعديل المستخدم برقم: ' . $user_id);
    header("Location: user_management.php");
    exit();
  } else {
    $error = "Failed to update user.";
    logUserAction($_SESSION['user_id'], 'edit_user_failed', 'users', 'فشل في تعديل المستخدم برقم: ' . $user_id);
  }
}

$roles_query = "SELECT * FROM roles";
$roles_result = $conn->query($roles_query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تعديل المستخدم</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
</head>

<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card my-5">
          <div class="card-header">
            <h3>تعديل المستخدم</h3>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">اسم المستخدم</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="first_name" class="form-label">الاسم الأول</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
              </div>
              <div class="mb-3">
                <label for="second_name" class="form-label">اسم الأب</label>
                <input type="text" class="form-control" id="second_name" name="second_name" value="<?php echo htmlspecialchars($user['second_name']); ?>">
              </div>
              <div class="mb-3">
                <label for="last_name" class="form-label">اللقب</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
              </div>
              <div class="mb-3">
                <label for="role_id" class="form-label">الدور</label>
                <select class="form-select" id="role_id" name="role_id" required>
                  <?php while ($role = $roles_result->fetch_assoc()): ?>
                    <option value="<?php echo $role['id']; ?>" <?php if ($role['id'] == $user['role_id']) echo 'selected'; ?>>
                      <?php echo htmlspecialchars($role['role_name']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">تحديث</button>
              <a href="user_management.php" class="btn btn-secondary">إلغاء</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>