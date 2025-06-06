<?php
include "../DB_connection.php";
include "../data/setting.php";
$setting = getSetting($conn);

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Student" || !isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get student id from session or user info
$student_id = $_SESSION['student_id'] ?? 0;

// Fetch student's class info
$student_sql = "SELECT grade, section FROM students WHERE student_id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bindValue(1, $student_id, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    exit;
}

// Fetch schedule entries for student's class
$sql = "SELECT s.schedule_id, sub.subject, s.day_of_week, s.start_time, s.end_time, t.fname, t.lname
        FROM schedule s
        JOIN subjects sub ON s.subject_id = sub.subject_id
        JOIN teachers t ON s.teacher_id = t.teacher_id
        JOIN class c ON s.class_id = c.class_id
        WHERE c.grade = ? AND c.section = ?
        ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.start_time";

$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $student['grade'], PDO::PARAM_INT);
$stmt->bindParam(2, $student['section'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - <?=$setting['school_name']?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include "inc/navbar.php"; ?>
<div class="container mt-5">
    <h2>Your Schedule</h2>
    <!-- Back to Main Page button removed as per request -->
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Subject</th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) { ?>
            <tr>
                <td><?=htmlspecialchars($row['subject'])?></td>
                <td><?=htmlspecialchars($row['day_of_week'])?></td>
                <td><?=htmlspecialchars($row['start_time'])?></td>
                <td><?=htmlspecialchars($row['end_time'])?></td>
                <td><?=htmlspecialchars($row['fname'] . ' ' . $row['lname'])?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function(){
        $("#navLinks li:nth-child(4) a").addClass('active');
    });
</script>
</body>
</html>
