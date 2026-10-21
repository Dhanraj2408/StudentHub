
<?php
require_once __DIR__ . "/db.php";

$course = trim($_GET["course"] ?? "");

try {
    if ($course !== "") {
        // Prepared statement: safely bind the user's course input
        $stmt = $pdo->prepare(
            "SELECT student_id, name, email, course, year
             FROM students
             WHERE course = :course
             ORDER BY name"
        );

        $stmt->execute(["course" => $course]);
    } else {
        // Display all students
        $stmt = $pdo->prepare(
            "SELECT student_id, name, email, course, year
             FROM students
             ORDER BY name"
        );

        $stmt->execute();
    }

    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Student query error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load students. Please check the database.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 25px;
        }

        main {
            max-width: 900px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        h1 {
            color: #1461b3;
        }

        select, button {
            padding: 10px;
            margin: 5px 0;
        }

        button {
            background: #1461b3;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #1461b3;
            color: white;
        }
    </style>
</head>
<body>
<main>
    <h1>StudentHub Students</h1>

    <form method="GET" action="students.php">
        <label for="course">Filter by course:</label>
        <select id="course" name="course">
            <option value="">All Courses</option>
            <?php
            $courses = ["BCA", "B.Tech", "B.Sc", "B.Com", "MCA"];
            foreach ($courses as $item):
            ?>
                <option
                    value="<?= e($item) ?>"
                    <?= $course === $item ? "selected" : "" ?>
                >
                    <?= e($item) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
        <a href="students.php">Show all</a>
    </form>

    <p>Records found: <strong><?= count($students) ?></strong></p>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5">No students found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= e($student["student_id"]) ?></td>
                            <td><?= e($student["name"]) ?></td>
                            <td><?= e($student["email"]) ?></td>
                            <td><?= e($student["course"]) ?></td>
                            <td><?= e($student["year"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p><a href="test_connection.php">Back to connection test</a></p>
</main>
</body>
</html>
