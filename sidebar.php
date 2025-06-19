<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" id="sidebar" aria-label="Main navigation">
  <div class="sidebar-header">
    <h1>ADMIN</h1>
  </div>
  <nav class="sidebar-nav" role="navigation">
    <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
      <span class="material-icons">dashboard</span>
      <span>Dashboard</span>
    </a>
    <a href="students.php" class="<?= $current_page === 'students.php' ? 'active' : '' ?>">
      <span class="material-icons">groups</span>
      <span>Students</span>
    </a>
    <a href="courses.php" class="<?= $current_page === 'courses.php' ? 'active' : '' ?>">
      <span class="material-icons">menu_book</span>
      <span>Courses</span>
    </a>
  </nav>

  <form method="post" action="logout.php" style="margin-top: auto;">
    <button type="submit" class="btn logout-btn">
      <span class="material-icons">logout</span>
      <span>Logout</span>
    </button>
  </form>
</aside>
