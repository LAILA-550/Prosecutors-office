<?php
$current_page = basename($_SERVER['REQUEST_URI']);
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse border-end">
  <div class="position-sticky" style="padding-top: 5rem;">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'admin' ? 'active text-white' : ''; ?> text-dark d-flex align-items-center" aria-current="page" href="/laila1/admin">
          <i class="bi bi-folder"></i>
          <span class="ms-2">إدارة القضايا</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'user_management.php' ? 'active text-white' : ''; ?> text-dark d-flex align-items-center" href="user_management.php">
          <i class="bi bi-people"></i>
          <span class="ms-2">إدارة المستخدمين</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'courts.php' ? 'active text-white' : ''; ?> text-dark d-flex align-items-center" href="courts.php">
          <i class="bi bi-court"></i>
          <span class="ms-2">إدارة المحاكم</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'police_departments.php' ? 'active text-white' : ''; ?> text-dark d-flex align-items-center" href="police_departments.php">
          <i class="bi bi-police"></i>
          <span class="ms-2">إدارة أقسام الشرطة</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $current_page == 'logs.php' ? 'active text-white' : ''; ?> text-dark d-flex align-items-center" href="logs.php">
          <i class="bi bi-journal-text"></i>
          <span class="ms-2">السجلات</span>
        </a>
      </li>
    </ul>
  </div>
</nav>