<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/add.css">
    <title>Add Employee</title>
</head> 

</head>
<body>

<div class="container">
    <header class="header-glowing">
        <nav class="nav">
            <a href="index.php">HOME</a>
            <a href="view.php">VIEW LIST</a>
        </nav>
    </header>
</div>
<div class="card">
        <h2>ADD EMPLOYEE</h2>
        <form method="post" id="employeeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <form method="post" id="employeeForm">

            <label for="employeeID">Employee ID:</label>
            <input type="text" id="ID" name="ID" required>

            <label for="firstName">First Name:</label>
            <input type="text" id="First_Name" name="First_Name" required>

            <label for="lastName">Last Name:</label>
            <input type="text" id="Last_Name" name="Last_Name" required>

            <label for="age">Age:</label>
            <input type="text" id="Age" name="Age" required>

            <label for="address">Address:</label>
            <input type="text" id="Address" name="Address" required>

            <label for="contactNumber">Contact Number:</label>
            <input type="text" id="Contact_Number" name="Contact_Number" required>

            <label for="Job_Description">Job Description:</label>
            <input type="text" id="Job_Description" name="Job_Description" required>

            <label for="Salary">Salary:</label>
            <input type="text" id="Salary" name="Salary" required>
            <br>
            <input type="hidden" name="action" value="insert">
            <button type="submit" value="submit">Submit</button>
        </form>
    </div>
    
    <?php
    // Include common functions and connect to the database
    include 'common.php';
    $conn = connectToDatabase();

    // Handle form submissions
    handleFormSubmissionEmployee($conn);

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
