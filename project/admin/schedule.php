<?php
include "../DB_connection.php";
include "../data/setting.php";
$setting = getSetting($conn);

// Check if user is admin
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin" || !isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Determine main page URL based on role
$role = $_SESSION['role'];
$mainPageUrl = "../login.php"; // default fallback
switch ($role) {
    case "Admin":
        $mainPageUrl = "../admin/index.php";
        break;
    case "Teacher":
        $mainPageUrl = "../Teacher/index.php";
        break;
    case "Student":
        $mainPageUrl = "../Student/index.php";
        break;
    case "RegistrarOffice":
        $mainPageUrl = "../RegistrarOffice/index.php";
        break;
}

// Fetch schedule entries with class, subject, teacher info
$sql = "SELECT s.schedule_id, c.grade, c.section, sub.subject, s.day_of_week, s.start_time, s.end_time, t.fname, t.lname
        FROM schedule s
        JOIN class c ON s.class_id = c.class_id
        JOIN subjects sub ON s.subject_id = sub.subject_id
        JOIN teachers t ON s.teacher_id = t.teacher_id
        ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.start_time";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management - <?=$setting['school_name']?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include "inc/navbar.php"; ?>
<div class="container mt-5">
    <h2>Schedule Management</h2>
    <!-- Back to Main Page button removed as per request -->
<a href="schedule-add.php" class="btn btn-dark mb-3 ms-2">Add Schedule Entry</a>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Grade</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) { ?>
            <tr>
                <td><?=htmlspecialchars($row['grade'])?></td>
                <td><?=htmlspecialchars($row['section'])?></td>
                <td><?=htmlspecialchars($row['subject'])?></td>
                <td><?=htmlspecialchars($row['day_of_week'])?></td>
                <td><?=htmlspecialchars($row['start_time'])?></td>
                <td><?=htmlspecialchars($row['end_time'])?></td>
                <td><?=htmlspecialchars($row['fname'] . ' ' . $row['lname'])?></td>
                <td>
                    <a href="schedule-edit.php?id=<?=intval($row['schedule_id'])?>" class="btn btn-primary btn-sm me-2">Edit</a>
                    <a href="schedule-delete.php?id=<?=intval($row['schedule_id'])?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function(){
        $("#navLinks li:nth-child(11) a").addClass('active');
    });
</script>
</body>
</html>
