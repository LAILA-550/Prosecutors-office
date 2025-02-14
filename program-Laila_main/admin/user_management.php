<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: /laila1/login.php");
    exit();
}

logUserAction($_SESSION['user_id'], 'access_user_management', 'users', 'تم الوصول إلى صفحة إدارة المستخدمين');

$query = "SELECT users.id, users.username, roles.role_name 
          FROM users 
          JOIN roles ON users.role_id = roles.id
          WHERE roles.role_name = 'prosecutor'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة أعضاء النيابة</title>
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
                    <h1 class="h2">إدارة المستخدمين</h1>
                    <?php include '../_navbar_contents.php' ?>
                </div>

                <div class="card my-4">
                    <div class="card-header">
                        <h3>المستخدمين</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">اسم المستخدم</th>
                                            <th scope="col">الدور</th>
                                            <th scope="col">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($user = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                                <td>
                                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">تعديل</a>
                                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger">حذف</a>
                                                    <a href="reset_password.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-secondary">إعادة تعيين كلمة المرور</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">لا يوجد أعضاء نيابة</p>
                        <?php endif; ?>
                        <a href="add_user.php" class="btn btn-primary">إضافة عضو جديد</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>