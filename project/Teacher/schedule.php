<?php
include "../DB_connection.php";
include "../data/setting.php";
$setting = getSetting($conn);

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher" || !isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get teacher id from session or user info
$teacher_id = $_SESSION['teacher_id'] ?? 0;

// Fetch schedule entries for this teacher
$sql = "SELECT s.schedule_id, c.grade, c.section, sub.subject, s.day_of_week, s.start_time, s.end_time
        FROM schedule s
        JOIN class c ON s.class_id = c.class_id
        JOIN subjects sub ON s.subject_id = sub.subject_id
        WHERE s.teacher_id = ?
        ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.start_time";

$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $teacher_id, PDO::PARAM_INT);
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

      <div class="table-responsive">
         <table class="table table-striped table-bordered align-middle">
           <thead class="table-light">
             <tr>
               <th>Grade</th>
               <th>Section</th>
               <th>Subject</th>
               <th>Day</th>
               <th>Start Time</th>
               <th>End Time</th>
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
             </tr>
             <?php } ?>
           </tbody>
         </table>
      </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
<script>
    $(document).ready(function(){
         $("#navLinks li:nth-child(4) a").addClass('active');
    });
</script>

</body>
</html>
