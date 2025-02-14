<?php
include "../session.php";
require_once('../connection.php');
require_once('../logger.php');

if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: /laila1/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


logUserAction($user_id, "زيارة صفحة الفهرس", "index.php", "نجاح");


$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
}

$cases = $conn->query("SELECT cases.id, cases.case_number, cases.defendant, cases.description, cases.status, 
                              police_departments.name AS police_department, courts.name AS court, 
                              CONCAT(prosecutor.first_name, ' ', prosecutor.second_name, ' ', prosecutor.last_name) AS prosecutor 
                       FROM cases 
                       JOIN case_assignments ON cases.id = case_assignments.case_id 
                       JOIN police_departments ON case_assignments.police_department_id = police_departments.id 
                       JOIN courts ON case_assignments.court_id = courts.id 
                       JOIN prosecutor ON cases.prosecutor_id = prosecutor.id
                       WHERE cases.case_number LIKE '$search_query' OR cases.defendant LIKE '%$search_query%'");

if (!$cases) {
    logUserAction($user_id, "فشل في جلب القضايا", "index.php", "فشل");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة القضايا</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <style>
        html,
        body {
            height: 100%;
        }

        .notification-icon {
            position: relative;
        }

        .notification-icon .badge {
            position: absolute;
            top: -10px;
            right: -10px;
        }
    </style>
</head>

<body class="bg-light h-100">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <?php include('_sidebar.php'); ?>


            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">إدارة القضايا</h1>
                    <?php include '../_navbar_contents.php' ?>
                </div>


                <form method="GET" action="index.php" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن قضية" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button class="btn btn-primary" type="submit">بحث</button>
                    </div>
                </form>


                <div class="card my-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>القضايا</h3>
                        <a href="add_case.php" class="btn btn-primary">إضافة قضية جديدة</a>
                    </div>
                    <div class="card-body">
                        <?php if ($cases->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">رقم القضية</th>
                                            <th scope="col">المدعى عليه</th>
                                            <th scope="col">الوصف</th>
                                            <th scope="col">الحالة</th>
                                            <th scope="col">قسم الشرطة</th>
                                            <th scope="col">المحكمة</th>
                                            <th scope="col">عضو النيابة</th>
                                            <th scope="col">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($case = $cases->fetch_assoc()): ?>

                                            <tr onclick="window.location='case_details.php?id=<?php echo $case['id']; ?>';" style="cursor:pointer;">
                                                <td><?php echo htmlspecialchars($case['case_number']); ?></td>
                                                <td><?php echo htmlspecialchars($case['defendant']); ?></td>
                                                <td><?php echo htmlspecialchars($case['description']); ?></td>
                                                <td><?php echo htmlspecialchars($case['status']); ?></td>
                                                <td><?php echo htmlspecialchars($case['police_department']); ?></td>
                                                <td><?php echo htmlspecialchars($case['court']); ?></td>
                                                <td><?php echo htmlspecialchars($case['prosecutor']); ?></td>
                                                <td>

                                                    <a href="edit_case.php?id=<?php echo $case['id']; ?>" class="btn btn-sm btn-warning" onclick="event.stopPropagation();">تعديل</a>
                                                    <a href="delete_case.php?id=<?php echo $case['id']; ?>" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); return confirm('هل أنت متأكد من حذف هذه القضية؟')">حذف</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">لا توجد قضايا.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5pN9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>