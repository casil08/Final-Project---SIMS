<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';
$mysqli = db_connect();

function getNextStudentID($mysqli) {
  $result = $mysqli->query("SELECT MAX(student_id) AS max_id FROM students");
  $row = $result->fetch_assoc();
  $next_id = $row && $row['max_id'] ? intval($row['max_id']) + 1 : 1;
  return str_pad($next_id, 9, '0', STR_PAD_LEFT);
}

// Handle Add Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_student'])) {
  $student_id = getNextStudentID($mysqli);
  $full_name = trim($_POST['full_name']);
  $email_address = trim($_POST['email_address']);
  $contact_no = trim($_POST['contact_no']);
  $course = $_POST['course'];
  $year_level = $_POST['year_level'];
  $gender = $_POST['gender'];

  // Check for duplicates
  $check_stmt = $mysqli->prepare("SELECT * FROM students WHERE full_name = ? AND email_address = ? AND contact_no = ?");
  $check_stmt->bind_param("sss", $full_name, $email_address, $contact_no);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();

  if ($check_result->num_rows > 0) {
    echo "<script>alert('Duplicate student detected! Same name, email, and contact number already exists.');</script>";
  } else {
    $stmt = $mysqli->prepare("INSERT INTO students (student_id, full_name, email_address, contact_no, course, year_level, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $student_id, $full_name, $email_address, $contact_no, $course, $year_level, $gender);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Student added successfully!'); location.href='students.php';</script>";
  }

  $check_stmt->close();
}


// Handle Edit Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_student'])) {
  $student_id = $_POST['student_id'];
  $full_name = $_POST['full_name'];
  $email_address = $_POST['email_address'];
  $contact_no = $_POST['contact_no'];
  $course = $_POST['course'];
  $year_level = $_POST['year_level'];
  $gender = $_POST['gender'];

  // Update query
  $stmt = $mysqli->prepare("UPDATE students SET full_name=?, email_address=?, contact_no=?, course=?, year_level=?, gender=? WHERE student_id=?");
  $stmt->bind_param("sssssss", $full_name, $email_address, $contact_no, $course, $year_level, $gender, $student_id);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Student updated successfully!'); location.href='students.php';</script>";
  exit;
}

// Handle Delete Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_student'])) {
  $delete_id = $mysqli->real_escape_string($_POST['delete_student_id']);
  $mysqli->query("DELETE FROM students WHERE student_id = '$delete_id'");
  echo "<script>alert('Student deleted successfully!'); location.href='students.php';</script>";
  exit();
}

$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';

$query = "
  SELECT 
    s.student_id, s.full_name, s.email_address, s.contact_no, 
    s.course, -- idagdag ito!
    s.year_level, s.gender, 
    c.course_name 
  FROM students s 
  LEFT JOIN courses c ON s.course = c.course_code
  ORDER BY s.student_id ASC
";

if (!empty($search)) {
  $query = "
    SELECT 
      s.student_id, s.full_name, s.email_address, s.contact_no, 
      s.year_level, s.gender, 
      c.course_name 
    FROM students s 
    LEFT JOIN courses c ON s.course = c.course_code
    WHERE s.full_name LIKE '%$search%' 
       OR s.email_address LIKE '%$search%' 
       OR c.course_name LIKE '%$search%'
    ORDER BY s.student_id ASC
  ";
}

$result = $mysqli->query($query);

// Pagination
$limit = 10; // students per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = $mysqli->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc();
$total_students = $total_result['total'];
$total_pages = ceil($total_students / $limit);

$query .= " LIMIT $limit OFFSET $offset";
$result = $mysqli->query($query);

// Fetch courses for dropdown
$courses_result = $mysqli->query("SELECT course_code, course_name FROM courses");
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
  $courses[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SIMS - Students</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/students.css" />
  <link rel="stylesheet" href="css/sidebar.css" />
  <link rel="stylesheet" href="css/header.css" />
  <style>
    .modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.3);
  align-items: center;
  justify-content: center;
}
.modal-content {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  min-width: 300px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
    .modal-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
    .modal-icon { background: #7c3aed; color: white; padding: 10px; border-radius: 50%; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 2rem; border-radius: 12px; max-width: 520px; width: 100%; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); animation: fadeIn 0.3s ease-in-out; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.4rem; }
    .form-group input, .form-group select { width: 100%; padding: 0.55rem; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95rem; }
    .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 1.5rem; }
    .btn { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s ease-in-out; }
    .btn.cancel { background-color: #ccc; color: #333; }
    .btn.cancel:hover { background-color: #bbb; }
    .btn.save { background-color: #7c3aed; color: white; }
    .btn.save:hover { background-color: #5b21b6; }
    @keyframes fadeIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .text-primary {
  color: #3498db !important;
}
.actions .material-icons {
  cursor: pointer;
  vertical-align: middle;
  font-size: 22px;
  transition: color 0.2s;
}
.actions .material-icons:hover {
  opacity: 0.8;
}
.page-link {
  display: inline-block;
  padding: 6px 14px;
  margin: 0 2px;
  border-radius: 5px;
  background: #f3f0ff !important;
  color: #7c3aed !important;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s, color 0.2s;
  border: 1px solid #e0e6ed !important;
}
.page-link.active, .page-link:hover {
  background: #7c3aed !important;
  color: #fff !important;
  border-color: #7c3aed !important;
}
.pagination {
  margin: 20px 0;
  text-align: center;
}
.pagination .page-link {
  display: inline-block;
  padding: 6px 14px;
  margin: 0 2px;
  border-radius: 5px;
  background: #f3f0ff;
  color: #7c3aed;
  text-decoration: none;
  font-weight: 600;
  border: 1px solid #e0e6ed;
  transition: background 0.2s, color 0.2s;
}
.pagination .page-link.active,
.pagination .page-link:hover {
  background: #7c3aed;
  color: #fff;
  border-color: #7c3aed;
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2>🧑🏼‍🎓 Students List</h2>
      <button type="button" class="btn-violet" onclick="openModal()"><i class="fas fa-plus"></i> Add Student</button>
    </div>

    <input type="text" id="liveSearch" placeholder="🔍 Search students..." style="margin-bottom: 1rem; padding: 8px; border: 1px solid #ccc; border-radius: 6px; width: 100%;" onkeyup="filterCourses()" />

    <div class="card-body table-responsive">
      <table class="table" id="courseTable">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact No.</th>
            <th>Course</th>
            <th>Year Level</th>
            <th>Gender</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email_address']) ?></td>
                <td><?= htmlspecialchars($row['contact_no']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['year_level']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td class="actions" style="text-align:center;">
  <a href="#" class="material-icons" style="color:#7c3aed;vertical-align:middle;font-size:22px;"
     title="Edit"
     onclick="openEditModal('<?= $row['student_id'] ?>', '<?= htmlspecialchars($row['full_name'], ENT_QUOTES) ?>', '<?= $row['email_address'] ?>', '<?= $row['contact_no'] ?>', '<?= $row['course'] ?>', '<?= $row['year_level'] ?>', '<?= $row['gender'] ?>'); return false;">
    edit
  </a>
  <a href="#" class="material-icons" style="color:#e74c3c;vertical-align:middle;font-size:22px;margin-left:8px;"
     title="Delete"
     onclick="openDeleteModal('<?= $row['student_id'] ?>'); return false;">
    delete
  </a>
</td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align: center;">No students found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
  <div class="pagination">
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

<!-- Add Student Modal -->
<div id="studentModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-icon"><i class="fas fa-user-plus"></i></span>
      <h2>Add New Student</h2>
    </div>
    <form method="post">
      <input type="hidden" name="add_student" value="1" />
      <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required /></div>
      <div class="form-group"><label>Email</label><input type="email" name="email_address" required /></div>
      <div class="form-group"><label>Contact No.</label><input type="text" name="contact_no" required /></div>
      <div class="form-group">
      <select name="course" class="form-control" required>
        <option value="" disabled selected>Select course</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= htmlspecialchars($course['course_code']) ?>">
            <?= htmlspecialchars($course['course_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      </div>
      <div class="form-group">
        <label>Year Level</label>
        <select name="year_level" required>
          <option value="">Select year</option>
          <option value="1st Year">1st Year</option>
          <option value="2nd Year">2nd Year</option>
          <option value="3rd Year">3rd Year</option>
          <option value="4th Year">4th Year</option>
        </select>
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender" required>
          <option value="">Select gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn save"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-icon"><i class="fas fa-user-edit"></i></span>
      <h2>Edit Student</h2>
    </div>
    <form method="post">
      <input type="hidden" name="edit_student" value="1" />
      <input type="hidden" name="student_id" id="edit_student_id" />
      <div class="form-group"><label>Full Name</label><input type="text" name="full_name" id="edit_full_name" required /></div>
      <div class="form-group"><label>Email</label><input type="email" name="email_address" id="edit_email_address" required /></div>
      <div class="form-group"><label>Contact No.</label><input type="text" name="contact_no" id="edit_contact_no" required /></div>
      <div class="form-group">
      <select name="course" id="edit_course" class="form-control" required>
        <option value="" disabled>Select course</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= htmlspecialchars($course['course_code']) ?>">
            <?= htmlspecialchars($course['course_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      </div>
      <div class="form-group">
        <label>Year Level</label>
        <select name="year_level" id="edit_year_level" required>
          <option value="">Select year</option>
          <option value="1st Year">1st Year</option>
          <option value="2nd Year">2nd Year</option>
          <option value="3rd Year">3rd Year</option>
          <option value="4th Year">4th Year</option>
        </select>
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender" id="edit_gender" required>
          <option value="">Select gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn save"><i class="fas fa-save"></i> Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Button -->
<i class="material-icons delete-btn" data-id="<?= $row['student_id'] ?>" data-toggle="modal" data-target="#deleteStudentModal">delete</i>

<!-- Delete Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" role="dialog" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST">
      <input type="hidden" name="delete_id" id="deleteStudentId">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete Student</h5>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this student?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_student" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Student Modal -->
<div id="deleteModal" class="modal">
  <form method="POST" class="modal-content">
    <input type="hidden" name="delete_student_id" id="delete_student_id" />
    <h3>Delete Student</h3>
    <p>Are you sure you want to delete this student?</p>
    <div style="margin-top:1.5rem;display:flex;gap:1rem;justify-content:flex-end;">
      <button type="submit" name="delete_student" class="btn" style="background:#e74c3c;color:#fff;">Delete</button>
      <button type="button" class="btn" style="background:#eee;color:#333;" onclick="hideModal('deleteModal')">Cancel</button>
    </div>
  </form>
</div>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<script>
  function openModal() {
    document.getElementById('studentModal').style.display = 'flex';
  }
  function closeModal() {
    document.getElementById('studentModal').style.display = 'none';
    document.querySelector('#studentModal form').reset();
  }

// For Delete Button
document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function () {
    const id = this.getAttribute('data-id');
    document.getElementById('deleteStudentId').value = id;
  });
});
 

  function openEditModal(id, name, email, contact, course, year, gender) {
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_full_name').value = name;
    document.getElementById('edit_email_address').value = email;
    document.getElementById('edit_contact_no').value = contact;
    document.getElementById('edit_course').value = course;
    document.getElementById('edit_year_level').value = year;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('editStudentModal').style.display = 'flex';
  }
  function closeEditModal() {
    document.getElementById('editStudentModal').style.display = 'none';
    document.querySelector('#editStudentModal form').reset();
  }

  window.onclick = function (e) {
    if (e.target.classList.contains('modal')) {
      closeModal();
      closeEditModal();
    }
  };


  function filterCourses() {
    const input = document.getElementById("liveSearch");
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll("#courseTable tbody tr");

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  }

  function showModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function hideModal(id) {
  document.getElementById(id).style.display = 'none';
}
function openDeleteModal(studentId) {
  document.getElementById('delete_student_id').value = studentId;
  showModal('deleteModal');
}
</script>

</body>
</html>
