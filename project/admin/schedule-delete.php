<?php
include "../DB_connection.php";
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
$stmt = $conn->prepare("DELETE FROM schedule WHERE schedule_id = ?");
$stmt->bindValue(1, $schedule_id, PDO::PARAM_INT);
$stmt->execute();

header("Location: schedule.php");
exit;
?>
