<?php
include "../session.php";
require "../connection.php";
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    session_unset();
    session_destroy();
    header("Location: /laila1/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

logUserAction($user_id, "زيارة صفحة إضافة قضية", "add_case.php", "نجاح");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $case_number = $_POST['case_number'];
    $defendant = $_POST['defendant'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $police_department_id = $_POST['police_department'];
    $court_id = $_POST['court'];
    $prosecutor_id = $_POST['prosecutor'];


    $query = "INSERT INTO cases (case_number, defendant, description, status, prosecutor_id) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $case_number, $defendant, $description, $status, $prosecutor_id);
    if ($stmt->execute()) {
        $case_id = $stmt->insert_id;


        $query = "INSERT INTO case_assignments (case_id, police_department_id, court_id) 
                  VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $case_id, $police_department_id, $court_id);
        if ($stmt->execute()) {
            logUserAction($user_id, "إضافة قضية", "cases", "نجاح: " . $case_number);
            header("Location: /laila1/admin");
            exit();
        } else {
            logUserAction($user_id, "فشل في تعيين القضية", "add_case.php", "فشل");
            echo "فشل في تعيين القضية.";
        }
    } else {
        logUserAction($user_id, "فشل في إضافة القضية", "add_case.php", "فشل");
        echo "فشل في إضافة القضية.";
    }
}


$police_departments = $conn->query("SELECT * FROM police_departments");
$courts = $conn->query("SELECT * FROM courts");
$prosecutors = $conn->query("SELECT * FROM prosecutor");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إضافة قضية</title>
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
                    <h1 class="h2">إضافة قضية جديدة</h1>
                </div>

                <div class="card my-4">
                    <div class="card-header">
                        <h3>تفاصيل القضية</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="case_number" class="form-label">رقم القضية:</label>
                                <input type="text" name="case_number" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="defendant" class="form-label">المدعى عليه:</label>
                                <input type="text" name="defendant" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف:</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">الحالة:</label>
                                <select name="status" class="form-select" required>
                                    <option value="قيد التحقيق">قيد التحقيق</option>
                                    <option value="مغلق">مغلق</option>
                                    <option value="مفتوح">مفتوح</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="prosecutor" class="form-label">عضو النيابة</label>
                                <select name="prosecutor" class="form-select">
                                    <?php while ($prosecutor = $prosecutors->fetch_assoc()): ?>
                                        <option value="<?php echo $prosecutor['id']; ?>"><?php echo $prosecutor['first_name'] . ' ' . $prosecutor['second_name'] . ' ' . $prosecutor['last_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="police_department" class="form-label">قسم الشرطة:</label>
                                <select name="police_department" class="form-select">
                                    <?php while ($department = $police_departments->fetch_assoc()): ?>
                                        <option value="<?php echo $department['id']; ?>"><?php echo $department['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="court" class="form-label">المحكمة:</label>
                                <select name="court" class="form-select">
                                    <?php while ($court = $courts->fetch_assoc()): ?>
                                        <option value="<?php echo $court['id']; ?>"><?php echo $court['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">إضافة القضية</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>