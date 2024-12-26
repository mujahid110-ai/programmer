<?php
include('db_config.php');

if (isset($_POST['submit'])) {
    // Collect and sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $semester = (int) $_POST['semester']; // Ensure semester is an integer

    // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO students (name, roll_number, course, class, semester) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $roll_number, $course, $class, $semester);

    if ($stmt->execute()) {
        echo "<div class='success-message'>Student added successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS file -->
    <style>
        /* Inline form-specific styling for better visuals */
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .success-message, .error-message {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            color: white;
            border-radius: 5px;
        }
        .success-message {
            background-color: #28a745;
        }
        .error-message {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Student</h2>
        <form action="add_student.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter student name" required>
            </div>
            <div class="form-group">
                <label for="roll_number">Roll Number:</label>
                <input type="text" id="roll_number" name="roll_number" placeholder="Enter roll number" required>
            </div>
            <div class="form-group">
                <label for="course">Course:</label>
                <input type="text" id="course" name="course" placeholder="Enter course" required>
            </div>
            <div class="form-group">
                <label for="class">Class:</label>
                <input type="text" id="class" name="class" placeholder="Enter class" required>
            </div>
            <div class="form-group">
                <label for="semester">Semester:</label>
                <input type="number" id="semester" name="semester" min="1" placeholder="Enter semester" required>
            </div>
            <input type="submit" name="submit" value="Add Student">
        </form>
    </div>
</body>
</html>
