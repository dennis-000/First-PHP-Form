<?php
// Database connection
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "admissions_db";
$port = 3307;

$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$errors = [];
$success_msg = "";

// Variables to hold user input after submission
$submitted_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $submitted_data = [
        "school_name" => trim($_POST["school_name"]),
        "dates_attended" => trim($_POST["dates_attended"]),
        "qualifications" => trim($_POST["qualifications"]),
        "wassce_index" => trim($_POST["wassce_index"]),
        "wassce_year" => trim($_POST["wassce_year"]),
        "wassce_month" => trim($_POST["wassce_month"]),
        "core_english" => trim($_POST["core_english"]),
        "core_maths" => trim($_POST["core_maths"]),
        "core_science" => trim($_POST["core_science"]),
        "core_social" => trim($_POST["core_social"]),
        "elective1" => trim($_POST["elective1"]),
        "elective2" => trim($_POST["elective2"]),
        "elective3" => trim($_POST["elective3"]),
        "elective4" => trim($_POST["elective4"])
    ];

    // Basic validation
    if (empty($submitted_data["school_name"]) || empty($submitted_data["wassce_index"])) {
        $errors[] = "School name and WASSCE Index Number are required.";
    }
    if (!preg_match("/^\d{10}$/", $submitted_data["wassce_index"])) {
        $errors[] = "WASSCE Index Number should be a 10-digit number.";
    }
    if (!empty($submitted_data["wassce_year"]) && !preg_match("/^\d{4}$/", $submitted_data["wassce_year"])) {
        $errors[] = "Year should be a four-digit number (e.g., 2024).";
    }

    // If no errors, insert data into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO admission 
            (school_name, dates_attended, qualifications, wassce_index, wassce_year, wassce_month, core_english, core_maths, core_science, core_social, elective1, elective2, elective3, elective4) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssss", $submitted_data["school_name"], $submitted_data["dates_attended"], $submitted_data["qualifications"], $submitted_data["wassce_index"], $submitted_data["wassce_year"], $submitted_data["wassce_month"], $submitted_data["core_english"], $submitted_data["core_maths"], $submitted_data["core_science"], $submitted_data["core_social"], $submitted_data["elective1"], $submitted_data["elective2"], $submitted_data["elective3"], $submitted_data["elective4"]);

        if ($stmt->execute()) {
            $success_msg = "Application submitted successfully!";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PU Undergraduate Admission Form</title>
    <link rel="stylesheet" href="puadmin.css">
</head>
<body>
    <div class="container">
        <h1>Pentecost University Undergraduate Admission Form</h1>

        <!-- Display Success Message -->
        <?php if (!empty($success_msg)): ?>
            <div class="success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Admission Form -->
        <form method="POST" action="">
            <h2>Educational Background</h2>
            <label>School Name: <input type="text" name="school_name" value="<?php echo htmlspecialchars($submitted_data['school_name'] ?? ''); ?>" required></label>
            <label>Dates Attended: <input type="text" name="dates_attended" value="<?php echo htmlspecialchars($submitted_data['dates_attended'] ?? ''); ?>"></label>
            <label>Qualifications Obtained: <input type="text" name="qualifications" value="<?php echo htmlspecialchars($submitted_data['qualifications'] ?? ''); ?>"></label>

            <h2>WASSCE Examination Details</h2>
            <label>Index Number: <input type="text" name="wassce_index" value="<?php echo htmlspecialchars($submitted_data['wassce_index'] ?? ''); ?>" required></label>
            <label>Year: <input type="text" name="wassce_year" value="<?php echo htmlspecialchars($submitted_data['wassce_year'] ?? ''); ?>" required></label>
            <label>Month: <input type="text" name="wassce_month" value="<?php echo htmlspecialchars($submitted_data['wassce_month'] ?? ''); ?>" required></label>

            <h3>Core Subjects</h3>
            <label>Core English: <input type="text" name="core_english" value="<?php echo htmlspecialchars($submitted_data['core_english'] ?? ''); ?>"></label>
            <label>Core Mathematics: <input type="text" name="core_maths" value="<?php echo htmlspecialchars($submitted_data['core_maths'] ?? ''); ?>"></label>
            <label>Core Science: <input type="text" name="core_science" value="<?php echo htmlspecialchars($submitted_data['core_science'] ?? ''); ?>"></label>
            <label>Core Social Studies: <input type="text" name="core_social" value="<?php echo htmlspecialchars($submitted_data['core_social'] ?? ''); ?>"></label>

            <h3>Elective Subjects</h3>
            <label>Elective 1: <input type="text" name="elective1" value="<?php echo htmlspecialchars($submitted_data['elective1'] ?? ''); ?>"></label>
            <label>Elective 2: <input type="text" name="elective2" value="<?php echo htmlspecialchars($submitted_data['elective2'] ?? ''); ?>"></label>
            <label>Elective 3: <input type="text" name="elective3" value="<?php echo htmlspecialchars($submitted_data['elective3'] ?? ''); ?>"></label>
            <label>Elective 4: <input type="text" name="elective4" value="<?php echo htmlspecialchars($submitted_data['elective4'] ?? ''); ?>"></label>

            <button type="submit">Submit</button>
        </form>

        <!-- Display Submitted Data -->
        <?php if (!empty($success_msg)): ?>
            <h2>Submitted Information</h2>
            <ul>
                <?php foreach ($submitted_data as $key => $value): ?>
                    <li><strong><?php echo ucfirst(str_replace("_", " ", $key)); ?>:</strong> 
                    <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
