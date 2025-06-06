<?php
include "../DB_connection.php";
include "../data/setting.php";
$setting = getSetting($conn);

// Check if user is admin
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $class_id = intval($_POST['class_id']);
        $subject_id = intval($_POST['subject_id']);
        $day_of_week = $_POST['day_of_week'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $teacher_id = intval($_POST['teacher_id']);

        // Validate that end_time is after start_time
        if ($end_time <= $start_time) {
            $error_message = "End time must be after start time.";
        } else {
            $stmt = $conn->prepare("INSERT INTO schedule (class_id, subject_id, day_of_week, start_time, end_time, teacher_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bindValue(1, $class_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $subject_id, PDO::PARAM_INT);
            $stmt->bindValue(3, $day_of_week, PDO::PARAM_STR);
            $stmt->bindValue(4, $start_time, PDO::PARAM_STR);
            $stmt->bindValue(5, $end_time, PDO::PARAM_STR);
            $stmt->bindValue(6, $teacher_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: schedule.php");
            exit;
        }
    }

// Fetch classes, subjects, teachers for dropdowns
$classes = $conn->query("SELECT class_id, grade, section FROM class ORDER BY grade, section");
$subjects = $conn->query("SELECT subject_id, subject FROM subjects ORDER BY subject");
$teachers = $conn->query("SELECT teacher_id, fname, lname FROM teachers ORDER BY fname, lname");

// Fetch all classes, subjects, teachers into arrays for reuse in form
$classes_arr = $classes->fetchAll(PDO::FETCH_ASSOC);
$subjects_arr = $subjects->fetchAll(PDO::FETCH_ASSOC);
$teachers_arr = $teachers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Schedule Entry - <?=$setting['school_name']?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include "inc/navbar.php"; ?>
<div class="container mt-4">
    <h2>Add Schedule Entry</h2>
    <form method="post" action="schedule-add.php">
        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger"><?=htmlspecialchars($error_message)?></div>
        <?php } ?>
        <div class="mb-3">
            <label for="class_id" class="form-label">Class</label>
            <select name="class_id" id="class_id" class="form-select" required>
                <?php foreach ($classes_arr as $row) { ?>
                    <option value="<?=intval($row['class_id'])?>">Grade <?=htmlspecialchars($row['grade'])?> - Section <?=htmlspecialchars($row['section'])?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject</label>
            <select name="subject_id" id="subject_id" class="form-select" required>
                <?php foreach ($subjects_arr as $row) { ?>
                    <option value="<?=intval($row['subject_id'])?>"><?=htmlspecialchars($row['subject'])?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="day_of_week" class="form-label">Day of Week</label>
            <select name="day_of_week" id="day_of_week" class="form-select" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Teacher</label>
            <select name="teacher_id" id="teacher_id" class="form-select" required>
                <?php foreach ($teachers_arr as $row) { ?>
                    <option value="<?=intval($row['teacher_id'])?>"><?=htmlspecialchars($row['fname'] . ' ' . $row['lname'])?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
        <a href="schedule.php" class="btn btn-secondary ms-2">Back to Schedule</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
