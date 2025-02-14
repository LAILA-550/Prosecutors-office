<?php
$user_id = $_SESSION['user_id'];
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id AND is_read = 0 ORDER BY created_at DESC");
?>

<div class="d-flex align-items-center">
  <h2 class="h5 me-3"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
  <div class="notification-icon">
    <a href="#" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="/laila1/assets/bell-solid.svg" alt="Notifications" />
      <?php if ($notifications->num_rows > 0): ?>
        <span class="badge bg-danger"><?php echo $notifications->num_rows; ?></span>
      <?php endif; ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <?php if ($notifications->num_rows > 0): ?>
        <?php while ($notification = $notifications->fetch_assoc()): ?>
          <li><a class="dropdown-item" href="#"><?php echo $notification['message']; ?></a></li>
        <?php endwhile; ?>
      <?php else: ?>
        <li><a class="dropdown-item text-muted" href="#">لا توجد إشعارات جديدة.</a></li>
      <?php endif; ?>
    </ul>
  </div>
  <a href="/laila1/logout.php" class="btn btn-outline-danger ms-3">تسجيل الخروج</a>
</div>