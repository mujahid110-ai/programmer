<?php
// Include the database connection
include 'db_config.php';

// Fetch subjects from the 'subjects' table
$subject_query = "SELECT id, name FROM subjects";
$subject_result = $conn->query($subject_query);
$subjects = $subject_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Subject</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Select Subject to View Attendance</h1>
    
    <!-- Subject Selection Form -->
    <form method="GET" action="view_attendance.php">
        <label for="subject">Select Subject:</label>
        <select name="subject_id" id="subject" required>
            <option value="" disabled selected>Select a subject</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['id']) ?>">
                    <?= htmlspecialchars($subject['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Attendance</button>
    </form>
</div>
</body>
</html>
