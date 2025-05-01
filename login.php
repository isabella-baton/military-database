<?php

    #database script
    require 'includes/database-connection.php';

    #starts the session
    session_start();

    #sets the error message for the popup later
    $error = "";

    #gets employee info
    function getInfo(PDO $pdo, string $employeeID) {
        $sql = "SELECT employeeID, password, department FROM employee WHERE employeeID = :employeeID";
        $info = pdo($pdo, $sql, ['employeeID' => $employeeID])->fetch();
        return $info;
    }

    #checks if request is post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        #retrieves info from the input
        $employeeID = $_POST['employeeID'];
        $password = $_POST['password'];

        #gets all other information
        $info = getInfo($pdo, $employeeID);

        if ($info && $password === $info['password']) {
            $_SESSION['employeeID'] = $employeeID;
            $_SESSION['department'] = $info['department'];

            #check the user's department
            if ($_SESSION['department'] === 'Admin') {
                #if user is admin, redirect to admin
                echo "Redirecting to admin.php";
				header("Location: admin.php");
                exit; #stop further script execution
            } else {
                #if user is not an admin, redirect to basic
                echo "Redirecting to basic.php";
				header("Location: basic.php");
                exit; #stop further script execution
            }
        } else {
            #invalid! show error
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
