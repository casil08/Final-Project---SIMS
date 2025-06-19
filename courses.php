<?php
$conn = new mysqli("localhost", "root", "", "sims - casil");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$successMessage = "";
$errorMessage = "";
$filter_department = isset($_GET['department']) ? $conn->real_escape_string($_GET['department']) : '';

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM courses";
if (!empty($filter_department)) {
  $query .= " WHERE department = '$filter_department'";
}
$query .= " ORDER BY course_code ASC LIMIT $limit OFFSET $offset";
$courses = $conn->query($query);

// Count total courses for pagination
$count_query = "SELECT COUNT(*) as total FROM courses";
if (!empty($filter_department)) {
  $count_query .= " WHERE department = '$filter_department'";
}
$count_result = $conn->query($count_query);
$total_courses = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_courses / $limit);

// Add Course
if (isset($_POST['add_course'])) {
  $code = trim($_POST['course_code']);
  $name = trim($_POST['course_name']);
  $dept = trim($_POST['department']);

  $check = $conn->prepare("SELECT * FROM courses WHERE course_code = ?");
  $check->bind_param("s", $code);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    header("Location: courses.php?error=Course code already exists!");
    exit();
  } else {
    $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, department) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $code, $name, $dept);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Course added successfully!'); location.href='courses.php';</script>";    exit();
  }
  $check->close();
}

// Edit Course
if (isset($_POST['edit_course'])) {
  $code = trim($_POST['course_code']);
  $name = trim($_POST['course_name']);
  $dept = trim($_POST['department']);

  $stmt = $conn->prepare("UPDATE courses SET course_name=?, department=? WHERE course_code=?");
  $stmt->bind_param("sss", $name, $dept, $code);
  $stmt->execute();
  $stmt->close();
  echo "<script>alert('Course updated successfully!'); location.href='courses.php';</script>";
  exit();
}

// Delete Course
if (isset($_POST['delete_course'])) {
  $code = $_POST['delete_code'];

  $stmt = $conn->prepare("DELETE FROM courses WHERE course_code=?");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $stmt->close();
  echo "<script>alert('Course deleted successfully!'); location.href='students.php';</script>";
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SIMS - Courses</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/courses.css" />
  <link rel="stylesheet" href="css/sidebar.css" />
  <link rel="stylesheet" href="css/header.css" />

  <style>
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.3s ease-in-out;
      font-family: 'Inter', sans-serif;
    }

    .modal-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .modal-icon {
      background: #5b21b6;
      color: white;
      padding: 10px;
      border-radius: 50%;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.4rem;
      color: #444;
    }

    .input-group {
      position: relative;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 0.95rem;
    }

    .input-group input,
    .input-group select {
      width: 100%;
      padding: 0.55rem 0.55rem 0.55rem 2.2rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 0.95rem;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 1.5rem;
    }

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: background 0.2s ease-in-out;
    }

    .btn.cancel {
      background-color: #ccc;
      color: #333;
    }

    .btn.cancel:hover {
      background-color: #bbb;
    }

    .btn.save {
      background-color: #7c3aed;
      color: white;
    }

    .btn.save:hover {
      background-color: #5b21b6;
    }



    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    .page-link {
  display: inline-block;
  padding: 6px 14px;
  margin: 0 2px;
  border-radius: 5px;
  background: #f3f0ff;
  color: #7c3aed;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s;
}
.page-link.active, .page-link:hover {
  background: #7c3aed;
  color: #fff;
}

.btn-violet {
  background: linear-gradient(90deg, #7c3aed 60%, #a78bfa 100%) !important;
  color: #fff !important;
  border: none !important;
  border-radius: 7px !important;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(124, 58, 237, 0.10);
  padding: 0.6rem 1.2rem;
  display: inline-block;
}
.btn-violet:hover {
  background: linear-gradient(90deg, #5b21b6 60%, #7c3aed 100%) !important;
}
    
  </style>
</head>
<body>
  <div class="app-container">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <main class="main-content">
      <div class="card-header">
        <h2>📚 Course List</h2>
        <button type="button" class="btn-violet" onclick="showModal('addModal')">+ Add Course</button>
      </div>

      <!-- 🔍 Live Search Field -->
      <input type="text" id="liveSearch" placeholder="🔍 Search courses..." style="margin-bottom: 1rem; padding: 8px; border: 1px solid #ccc; border-radius: 6px; width: 100%;" onkeyup="filterCourses()" />

      <?php if ($successMessage): ?>
        <div class="alert"><?= $successMessage ?></div>
      <?php endif; ?>
      <?php if ($errorMessage): ?>
        <div class="alert error"><?= $errorMessage ?></div>
        <script>showModal('addModal');</script>
      <?php endif; ?>
<?php if (isset($_GET['success'])): ?>
  <div class="alert"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
  <script>showModal('addModal');</script>
<?php endif; ?>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Department</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="courseTable">
            <?php while ($row = $courses->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['course_code']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td class="actions" style="display:flex; align-items:center; justify-content:center; gap:10px; height:100%;">
  <a href="#" class="material-icons"
     style="color:#7c3aed; font-size:22px; display:flex; align-items:center; justify-content:center;"
     onclick='openEditModal(<?= json_encode($row) ?>)'>edit</a>
  <a href="#" class="material-icons"
     style="color:#e74c3c; font-size:22px; display:flex; align-items:center; justify-content:center;"
     onclick='openDeleteModal("<?= $row["course_code"] ?>")'>delete</a>
</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
<?php if ($total_pages > 1): ?>
  <div class="pagination" style="margin:20px 0; text-align:center;">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page-1 ?>" class="page-link">&laquo; Prev</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?= $i ?>" class="page-link<?= $i == $page ? ' active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
      <a href="?page=<?= $page+1 ?>" class="page-link">Next &raquo;</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
    </main>
  </div>

  <!-- Add Modal -->
  <div id="addModal" class="modal">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <span class="modal-icon"><i class="fas fa-plus"></i></span>
        <h2>Add New Course</h2>
      </div>

      <div class="form-group">
        <label for="add_code">Course Code</label>
        <div class="input-group">
          <i class="fas fa-code"></i>
          <input type="text" id="add_code" name="course_code" required />
        </div>
      </div>

      <div class="form-group">
        <label for="add_name">Course Name</label>
        <div class="input-group">
          <i class="fas fa-book"></i>
          <input type="text" id="add_name" name="course_name" required />
        </div>
      </div>

      <div class="form-group">
        <label for="add_dept">Department</label>
        <div class="input-group">
          <i class="fas fa-building"></i>
          <select id="add_dept" name="department" required>
            <option value="">Select Department</option>
            <option value="School of Business (SOB)">School of Business (SOB)</option>
            <option value="School of Arts, Sciences, & Technology (SOAST)">School of Arts, Sciences, & Technology (SOAST)</option>
            <option value="School of Teacher Education (SOTE)">School of Teacher Education (SOTE)</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="hideModal('addModal')">Cancel</button>
         <button type="submit" name="add_course" class="btn save">Save</button>
      </div>
    </form>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <span class="modal-icon"><i class="fas fa-pen-to-square"></i></span>
        <h2>Edit Course</h2>
      </div>

      <input type="hidden" name="course_code" id="edit_code" />

      <div class="form-group">
        <label for="edit_name">Course Name</label>
        <div class="input-group">
          <i class="fas fa-book"></i>
          <input type="text" name="course_name" id="edit_name" required />
        </div>
      </div>

      <div class="form-group">
        <label for="edit_dept">Department</label>
        <div class="input-group">
          <i class="fas fa-building"></i>
          <select id="edit_dept" name="department" required>
            <option value="">Select Department</option>
            <option value="School of Business (SOB)">School of Business (SOB)</option>
            <option value="School of Arts, Sciences, & Technology (SOAST)">School of Arts, Sciences, & Technology (SOAST)</option>
            <option value="School of Teacher Education (SOTE)">School of Teacher Education (SOTE)</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="hideModal('editModal')">Cancel</button>
         <button type="submit" name="edit_course" class="btn save">Update</button>
      </div>
    </form>
  </div>

  <!-- Delete Modal -->
  <div id="deleteModal" class="modal">
    <form method="POST" class="modal-content">
      <input type="hidden" name="delete_code" id="delete_code" />
      <h3>Delete Course</h3>
      <p>Are you sure you want to delete this course?</p>
      <div class="form-actions">
        <button type="submit" name="delete_course" class="btn save" style="background:#e74c3c;">Delete</button>
        <button type="button" class="btn cancel" onclick="hideModal('deleteModal')">Cancel</button>
      </div>
    </form>
  </div>

  <script>
    function showModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function hideModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function openEditModal(data) {
      document.getElementById('edit_code').value = data.course_code;
      document.getElementById('edit_name').value = data.course_name;
     const dept = data.department.trim();
  const select = document.getElementById('edit_dept');

  for (let i = 0; i < select.options.length; i++) {
    if (select.options[i].value.trim() === dept) {
      select.selectedIndex = i;
      break;
    }
  }

  showModal('editModal');
}
    function openDeleteModal(code) {
      document.getElementById('delete_code').value = code;
      showModal('deleteModal');
    }
    
    // Live Search Function
    function filterCourses() {
      const input = document.getElementById("liveSearch");
      const filter = input.value.toLowerCase();
      const rows = document.querySelectorAll("#courseTable tr");

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    }

  </script>
</body>
</html>
