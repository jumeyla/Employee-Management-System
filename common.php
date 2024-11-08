<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/common.css">
</head>
</html>
<?php
ini_set('memory_limit', '512M');

function connectToDatabase(){
    $servername = "localhost";
    $username = "Radge";
    $password = "1234453";
    $dbname = "ems";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
function executeQuery($conn, $sql, $params = []){
    try {
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        // Check if it's a SELECT query
        $isSelectQuery = stripos($sql, 'SELECT') === 0;

        if (!$isSelectQuery && !empty($params)) {
            // For non-SELECT queries, proceed with binding parameters
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if ($isSelectQuery) {
            $result = $stmt->get_result();

            if ($result === FALSE) {
                throw new Exception($stmt->error);
            }

            return $result;
        }

        return true; // For non-SELECT queries
    } catch (Exception $e) {
        // Log the exception or handle it appropriately
        echo "Error: " . $e->getMessage();
    
        // Close the statement if it's not null
        if ($stmt !== null) {
            $stmt->close();
        }
    
        // Close the connection and reconnect
        $conn->close();
        $conn = connectToDatabase();
    
        // Retry the query
        return executeQuery($conn, $sql, $params);
    }    
}
function displayEmployeeData($conn) {
    $sql_select = "SELECT * FROM employee";
    $result = executeQuery($conn, $sql_select);
    $employeeCount = 11;

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<thead>";
        echo "<tr>
                <th>Number</th>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Contact Number</th>
                <th>Job Description</th>
                <th>Address</th>
                <th>Salary</th>
                <th>Action</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = $result->fetch_assoc()) {
            $remainingCount = max(0, $employeeCount - 1);
            echo "<tr>";
            echo "<td>" . $remainingCount . "</td>";
            echo "<td>" . $row["ID"] ."</td>";
            echo "<td>" . $row["First_Name"] . "</td>";
            echo "<td>" . $row["Last_Name"] . "</td>";
            echo "<td>" . $row["Age"] . "</td>";
            echo "<td>" . $row["Contact_Number"] . "</td>";
            echo "<td>" . $row["Job_Description"] . "</td>";
            echo "<td>" . $row["Address"] . "</td>";
            echo "<td>" . $row["Salary"] . "</td>";
            echo "<td>";
            echo "<div style='display: inline-block;'>";
            echo "<form action='update.php' method='get'>";
            echo "<input type='hidden' name='id' value='" . $row["ID"] . "'>";
            echo "<input type='hidden' name='fn' value='" . $row["First_Name"] . "'>";
            echo "<input type='hidden' name='ln' value='" . $row["Last_Name"] . "'>";
            echo "<input type='hidden' name='jd' value='" . $row["Age"] . "'>";
            echo "<input type='hidden' name='id' value='" . $row["Contact_Number"] . "'>";
            echo "<input type='hidden' name='fn' value='" . $row["Job_Description"] . "'>";
            echo "<input type='hidden' name='ln' value='" . $row["Address"] . "'>";
            echo "<input type='hidden' name='jd' value='" . $row["Salary"] . "'>";
            echo "<input type='submit' value='Update' style='background-color: #007bff; color: #ffffff; border: none; padding: 8px 17px; border-radius: 4px; cursor: pointer;'>";
            echo "</form>";
            echo "</div>";
            echo "<div style='display: inline-block;'>";
            echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
            echo "<input type='hidden' name='ID' value='".$row["ID"]."'>";
            echo "<input type='hidden' name='action' value='delete'>";
            echo "<input type='submit' value='Delete' style='background-color: #dc3545; color: #ffffff; border: none; padding: 8px 19px; border-radius: 5px; cursor: pointer;'>";
            echo "</form>";
            echo "</div>";
            echo "</td>";
            echo "</tr>";
            $employeeCount--;
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "No records found.";
    }
}
function getCurrentEmployeeCount($conn) {
    $sql = "SELECT COUNT(ID) as count FROM employee";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}
function handleFormSubmissionEmployee($conn) {
    // Error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Maximum number of employees allowed
    $maxEmployees = 10;

    // Get form data
    $ID = $_POST["ID"] ?? '';
    $First_Name = $_POST["First_Name"] ?? '';
    $Last_Name = $_POST["Last_Name"] ?? '';
    $Age = $_POST["Age"] ?? '';
    $Contact_Number =  $_POST["Contact_Number"] ?? '';
    $Job_Description = $_POST["Job_Description"] ?? '';
    $Address = $_POST["Address"] ?? '';
    $Salary = $_POST["Salary"] ?? '';
    $action = $_POST["action"] ?? '';

    // Handle form actions
    if ($action == "insert") {
        // Get current employee count
        $employeeCount = getCurrentEmployeeCount($conn);

        // Check if current count is less than maximum
        if ($employeeCount < $maxEmployees) {
            // Proceed with insertion
            $sql = "INSERT INTO employee (ID, First_Name, Last_Name, Age, Contact_Number, Job_Description, Address, Salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $ID, $First_Name, $Last_Name, $Age, $Contact_Number, $Job_Description, $Address, $Salary);
            if ($stmt->execute()) {
                // Successful addition
            } else {
                echo "Error inserting record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Maximum employees reached
            echo "Cannot add more employees. Maximum limit reached.";
        }
    } elseif ($action == "update") {
        // Update employee
        $newID = $_POST["newID"] ?? ''; 
        $sql = "UPDATE employee SET ID=?, First_Name=?, Last_Name=?, Age=?, Contact_Number=?, Job_Description=?, Address=?, Salary=? WHERE ID=?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $newID, $First_Name, $Last_Name, $Age, $Contact_Number, $Job_Description, $Address, $Salary, $ID);
        if ($stmt->execute()) {
            // Successful update
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close(); 
    } elseif ($action == "delete") {
        // Delete employee
        $sql = "DELETE FROM employee WHERE ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ID);
        if ($stmt->execute()) {
            // Successful deletion
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
}