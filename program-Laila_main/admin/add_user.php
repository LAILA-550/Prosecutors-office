<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: /laila1/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash($username, PASSWORD_DEFAULT);
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $second_name = htmlspecialchars(trim($_POST['second_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $role_id = htmlspecialchars($_POST['role_id']);

    // Validate input
    if (empty($username) || empty($password) || empty($role_id)) {
        echo "الرجاء ملء جميع الحقول المطلوبة.";
        exit();
    }

    $query = "INSERT INTO users (username, password, role_id) VALUES (?, ?, 2)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        $query = "INSERT INTO prosecutor (id, first_name, second_name, last_name) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $first_name, $second_name, $last_name);
        $stmt->execute();


        logUserAction($_SESSION['user_id'], 'add_user', 'users', 'تم إضافة المستخدم برقم: ' . $user_id);

        header("Location: user_management.php");
        exit();
    } else {
        echo "فشل في إضافة المستخدم.";
        logUserAction($_SESSION['user_id'], 'add_user_failed', 'users', 'فشل في إضافة المستخدم: ' . $username);
    }
}


$roles = $conn->query("SELECT * from roles");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إضافة عضو نيابة</title>
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
                    <h1 class="h2">إضافة مستخدم جديد</h1>
                </div>

                <div class="card my-4">
                    <div class="card-header">
                        <h3>تفاصيل المستخدم</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" id="userForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">اسم المستخدم:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">الاسم الأول:</label>
                                <input type="text" name="first_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="second_name" class="form-label">الاسم الأب:</label>
                                <input type="text" name="second_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">اللقب:</label>
                                <input type="text" name="last_name" class="form-control">
                            </div>
                            <!-- <div class="mb-3">
                                <label for="role_id" class="form-label">الدور:</label>
                                <select name="role_id" class="form-select" id="roleSelect" required>
                                    <?php while ($role = $roles->fetch_assoc()): ?>
                                        <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div> -->
                            <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('roleSelect').addEventListener('change', function() {
            var role = this.value;
            var firstName = document.querySelector('input[name="first_name"]');
            var secondName = document.querySelector('input[name="second_name"]');
            var lastName = document.querySelector('input[name="last_name"]');

            if (role == '2') {
                firstName.required = true;
                secondName.required = true;
                lastName.required = true;
            } else {
                firstName.required = false;
                secondName.required = false;
                lastName.required = false;
            }
        });
    </script>
</body>

</html>