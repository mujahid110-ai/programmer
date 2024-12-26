<?php
// Include the database connection
include 'db_config.php';

// Fetch the subjects for the dropdown menu
$subject_query = "SELECT * FROM subjects";
$subject_result = $conn->query($subject_query);
$subjects = $subject_result->fetch_all(MYSQLI_ASSOC);
$subject_result->free(); // Free the result set

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $attendance = $_POST['attendance'] ?? [];

    // Validate inputs
    if (empty($subject_id) || empty($date) || empty($attendance)) {
        die("Error: All fields are required.");
    }

    // Prepare SQL to insert data into the attendance table
    $stmt = $conn->prepare("INSERT INTO attendance (roll_number, subject_id, date, status) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Insert each attendance record
    foreach ($attendance as $roll_number => $status) {
        // Bind and execute the insertion statement
        $stmt->bind_param('siss', $roll_number, $subject_id, $date, $status);
        if (!$stmt->execute()) {
            echo "Error executing query for roll_number: $roll_number<br>";
            echo "Error: " . $stmt->error . "<br>";
        }
    }

    $stmt->close(); // Close the prepared statement
    echo "Attendance successfully recorded!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Attendance</title>
    <link rel="stylesheet" href="attendance_styles.css">
</head>
<body>
<div class="container">
    <h1>Record Attendance</h1>
    <form method="POST" action="">
        <label for="subject">Select Subject:</label>
        <select name="subject_id" id="subject" required>
            <option value="" disabled selected>-- Select Subject --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['id']) ?>">
                    <?= htmlspecialchars($subject['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required>

        <table>
            <thead>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Attendance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Fetch students for marking attendance
            $students_query = "SELECT roll_number, name FROM students";
            $students_result = $conn->query($students_query);
            $students = $students_result->fetch_all(MYSQLI_ASSOC);
            $students_result->free(); // Free the result set

            foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['roll_number']) ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td>
                        <label>
                            <input type="radio" name="attendance[<?= $student['roll_number'] ?>]" value="Present" required> Present
                        </label>
                        <label>
                            <input type="radio" name="attendance[<?= $student['roll_number'] ?>]" value="Absent" required> Absent
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit">Submit Attendance</button>
    </form>
</div>
</body>
</html>
