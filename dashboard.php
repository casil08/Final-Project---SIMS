<?php
$conn = new mysqli("localhost", "root", "", "sims - casil");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Count total students
$studentCountResult = $conn->query("SELECT COUNT(*) AS total_students FROM students");
$studentCountRow = $studentCountResult->fetch_assoc();
$totalStudents = $studentCountRow['total_students'];

// Count total courses
$courseCountResult = $conn->query("SELECT COUNT(*) AS total_courses FROM courses");
$courseCountRow = $courseCountResult->fetch_assoc();
$totalCourses = $courseCountRow['total_courses'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIMS - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <style>
    .main-wrapper {
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
      padding: 0 1rem;
    }
  </style>
</head>
<body>

<div class="app-container">
  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <main class="app-main" role="main">
    <div class="main-wrapper">
      <section class="welcome-banner panel">
        <h2>👋 Welcome back, Admin!</h2>
        <p>Here's a quick overview of your system today.</p>
      </section>

      <section class="summary-cards" aria-label="Summary information panels">
        <article class="summary-card" role="region" aria-labelledby="students-title">
          <div class="summary-icon icon-students" aria-hidden="true">
            <span class="material-icons" style="font-size: 28px;">school</span>
          </div>
          <div class="summary-text">
            <div class="label" id="students-title">Total Students</div>
            <div class="value"><?php echo $totalStudents; ?></div>
          </div>
        </article>
        <article class="summary-card" role="region" aria-labelledby="courses-title">
          <div class="summary-icon icon-courses" aria-hidden="true">
            <span class="material-icons" style="font-size: 28px;">menu_book</span>
          </div>
          <div class="summary-text">
            <div class="label" id="courses-title">Total Courses</div>
            <div class="value"><?php echo $totalCourses; ?></div>
          </div>
        </article>
      </section>

      <section class="calendar-section panel">
        <div class="calendar-header">
          <button class="nav-btn" id="prevMonth" aria-label="Previous Month">◀</button>
          <span id="calendarTitle">June 2025</span>
          <button class="nav-btn" id="nextMonth" aria-label="Next Month">▶</button>
        </div>
        <div id="calendar"></div>
      </section>
    </div>
  </main>
</div>

<script>
  const calendar = document.getElementById("calendar");
  const calendarTitle = document.getElementById("calendarTitle");
  const prevBtn = document.getElementById("prevMonth");
  const nextBtn = document.getElementById("nextMonth");

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
  const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

  let currentDate = new Date();

  function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    calendarTitle.textContent = `${monthNames[month]} ${year}`;

    let html = `<div class="calendar-grid">`;
    dayNames.forEach(day => {
      html += `<div class="calendar-day-name">${day}</div>`;
    });

    for (let i = 0; i < firstDay; i++) {
      html += `<div class="calendar-day empty"></div>`;
    }

    for (let d = 1; d <= lastDate; d++) {
      const isToday = (
        d === new Date().getDate() &&
        month === new Date().getMonth() &&
        year === new Date().getFullYear()
      );

      html += `<div class="calendar-day${isToday ? ' today' : ''}">${d}</div>`;
    }

    html += `</div>`;
    calendar.innerHTML = html;
  }

  prevBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  });

  nextBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  });

  renderCalendar(currentDate);
</script>
</body>
</html>
