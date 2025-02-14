<?php
include "session.php";
require_once('connection.php');

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role_id'] == 1) {
        header("Location: admin");
    } else {
        header("Location: cases.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // $query = "UPDATE users SET password = ? WHERE username = ?";
    // $stmt = $conn->prepare($query);
    // $stmt->bind_param("ss", password_hash($password, PASSWORD_DEFAULT), $username);
    // $stmt->execute();
    // exit();

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['user_id'] = $user['id'];

            if ($password == $username) {
                $_SESSION['change_password'] = true;
                header("Location: change_password.php");
                logUserAction($user['id'], 'login', '/laila1/login.php', 'تحويل لتغيير كلمة المرور');
                exit();
            }

            if ($user['role_id'] == 1) {
                header("Location: admin");
            } else {
                header("Location: cases.php");
            }

            logUserAction($user['id'], 'login', '/laila1/login.php', 'تم تسجيل الدخول');
            exit();
        } else {
            $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة.";
        }
    } else {
        $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة.";
    }
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h1 class="text-center my-5">تسجيل الدخول</h1>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>