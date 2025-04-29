<?php
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection script
    require 'includes/database-connection.php';

    // Start the session
    session_start();

    // Set error message to empty for later handling
    $error = "";

    // Get employee information
    function getInfo(PDO $pdo, string $employeeID) {
        $sql = "SELECT employeeID, password, department FROM employee WHERE employeeID = :employeeID";
        $info = pdo($pdo, $sql, ['employeeID' => $employeeID])->fetch();
        return $info;
    }

    // Check if request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve inputted info
        $employeeID = $_POST['employeeID'];
        $password = $_POST['password'];

        // Retrieve employee info
        $info = getInfo($pdo, $employeeID);

        // Ensure there is info AND their password is correct
        if ($info && $password === $info['password']) {
            $_SESSION['employeeID'] = $employeeID;
            $_SESSION['department'] = $info['department'];

            // Check the user's department stored in the session
            if ($_SESSION['department'] === 'Admin') {
                // If the user is an Admin, redirect them to the admin dashboard
                header("Location: admin.php");
                exit; // Stop further script execution after redirect
            } else {
                // If the user is not an Admin, redirect them to the basic user dashboard
                header("Location: basic.php");
                exit; // Stop further script execution after redirect
            }
        } else {
            // Invalid login - show error message
            $error = "Invalid login - please try again.";
            echo "<script>alert('$error');</script>";
        }
    }
?> 

<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Military Database Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <main>
        <div class="login-container">
            <h1>Login</h1>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="employeeID">Employee ID:</label>
                    <input type="text" id="employeeID" name="employeeID" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Submit</button>
            </form>

        </div>
    </main>
</body>

</html>
