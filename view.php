<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/view.css">
    <title>View List</title>
</head>

<body>
<div class="container">
    <header class="header-glowing">
        <nav class="nav">
            <a href="index.php">HOME</a>
            <a href="add.php">ADD EMPLOYEE</a>
        </nav>
    </header>
</div>
<br>
<br>
<?php
// Include common functions and connect to the database
include 'common.php';
$conn = connectToDatabase();

// Handle The display of data
displayEmployeeData($conn);

//Handle Form Submission
handleFormSubmissionEmployee($conn);

// Close the database connection
$conn->close();
?>
</body>
</html>
