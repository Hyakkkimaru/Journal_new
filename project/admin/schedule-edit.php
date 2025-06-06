<?php
include "../DB_connection.php";
include "../data/setting.php";
$setting = getSetting($conn);

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: schedule.php");
    exit;
}

$schedule_id = intval($_GET['id']);

// Fetch existing schedule entry
$stmt = $conn->prepare("SELECT * FROM schedule WHERE schedule_id = ?");
$stmt->bindValue(1, $schedule_id, PDO::PARAM_INT);
$stmt->execute();
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Schedule entry not found.</div></div>";
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
        $update_stmt = $conn->prepare("UPDATE schedule SET class_id = ?, subject_id = ?, day_of_week = ?, start_time = ?, end_time = ?, teacher_id = ? WHERE schedule_id = ?");
        $update_stmt->bindValue(1, $class_id, PDO::PARAM_INT);
        $update_stmt->bindValue(2, $subject_id, PDO::PARAM_INT);
        $update_stmt->bindValue(3, $day_of_week, PDO::PARAM_STR);
        $update_stmt->bindValue(4, $start_time, PDO::PARAM_STR);
        $update_stmt->bindValue(5, $end_time, PDO::PARAM_STR);
        $update_stmt->bindValue(6, $teacher_id, PDO::PARAM_INT);
        $update_stmt->bindValue(7, $schedule_id, PDO::PARAM_INT);
        $update_stmt->execute();

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
    <title>Edit Schedule Entry - <?=$setting['school_name']?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Edit Schedule Entry</h2>
<form method="post" action="schedule-edit.php?id=<?=$schedule_id?>">
    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?=htmlspecialchars($error_message)?></div>
    <?php } ?>
    <div class="mb-3">
        <label for="class_id" class="form-label">Class</label>
        <select name="class_id" id="class_id" class="form-select" required>
            <?php foreach ($classes_arr as $row) { ?>
                <option value="<?=intval($row['class_id'])?>" <?=($row['class_id'] == $schedule['class_id']) ? 'selected' : ''?>>
                    Grade <?=htmlspecialchars($row['grade'])?> - Section <?=htmlspecialchars($row['section'])?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="subject_id" class="form-label">Subject</label>
        <select name="subject_id" id="subject_id" class="form-select" required>
            <?php foreach ($subjects_arr as $row) { ?>
                <option value="<?=intval($row['subject_id'])?>" <?=($row['subject_id'] == $schedule['subject_id']) ? 'selected' : ''?>>
                    <?=htmlspecialchars($row['subject'])?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="day_of_week" class="form-label">Day of Week</label>
        <select name="day_of_week" id="day_of_week" class="form-select" required>
            <?php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $selected = ($day == $schedule['day_of_week']) ? 'selected' : '';
                echo "<option value=\"$day\" $selected>$day</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Start Time</label>
        <input type="time" name="start_time" id="start_time" class="form-control" value="<?=htmlspecialchars($schedule['start_time'])?>" required />
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">End Time</label>
        <input type="time" name="end_time" id="end_time" class="form-control" value="<?=htmlspecialchars($schedule['end_time'])?>" required />
    </div>
    <div class="mb-3">
        <label for="teacher_id" class="form-label">Teacher</label>
        <select name="teacher_id" id="teacher_id" class="form-select" required>
            <?php foreach ($teachers_arr as $row) { ?>
                <option value="<?=intval($row['teacher_id'])?>" <?=($row['teacher_id'] == $schedule['teacher_id']) ? 'selected' : ''?>>
                    <?=htmlspecialchars($row['fname'] . ' ' . $row['lname'])?>
                </option>
            <?php } ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Schedule</button>
    <a href="schedule.php" class="btn btn-secondary ms-2">Cancel</a>
</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
