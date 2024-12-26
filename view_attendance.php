<?php
// Include the database connection
include 'db_config.php';

// Fetch subjects for subject selection
$subject_query = "SELECT * FROM subjects";
$subject_result = $conn->query($subject_query);
$subjects = $subject_result->fetch_all(MYSQLI_ASSOC);

// Check if the subject is selected
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    
    // Get total lectures conducted for the subject
    $total_lectures_query = "
        SELECT COUNT(DISTINCT date) AS total_lectures
        FROM attendance
        WHERE subject_id = ?
    ";
    $stmt = $conn->prepare($total_lectures_query);
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $total_lectures_result = $stmt->get_result();
    $total_lectures = $total_lectures_result->fetch_assoc()['total_lectures'];
    $stmt->close();

    // Fetch student attendance stats
    $attendance_query = "
        SELECT s.roll_number, s.name,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS lectures_present
        FROM students s
        LEFT JOIN attendance a ON s.roll_number = a.roll_number AND a.subject_id = ?
        GROUP BY s.roll_number, s.name
    ";
    $stmt = $conn->prepare($attendance_query);
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $attendance_result = $stmt->get_result();
    $attendance_records = $attendance_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #6c757d;
        }
        form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Attendance Records for Selected Subject</h1>

    <!-- Subject Dropdown -->
    <form method="GET" action="">
        <label for="subject"><strong>Select Subject:</strong></label>
        <select name="subject_id" id="subject" required>
            <option value="" disabled selected>-- Select Subject --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['id']) ?>" <?= isset($subject_id) && $subject_id == $subject['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($subject['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Attendance</button>
    </form>

    <?php if (isset($attendance_records)): ?>
        <h2>Total Lectures Conducted: <?= $total_lectures ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th>Total Lectures</th>
                    <th>Lectures Present</th>
                    <th>Lectures Absent</th>
                    <th>Attendance Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $record): 
                    $lectures_present = $record['lectures_present'] ?? 0;
                    $lectures_absent = $total_lectures - $lectures_present;
                    $attendance_percentage = $total_lectures > 0 
                        ? round(($lectures_present / $total_lectures) * 100, 2) 
                        : 0;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($record['roll_number']) ?></td>
                        <td><?= htmlspecialchars($record['name']) ?></td>
                        <td><?= $total_lectures ?></td>
                        <td><?= $lectures_present ?></td>
                        <td><?= $lectures_absent ?></td>
                        <td><?= $attendance_percentage ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
