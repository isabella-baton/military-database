<?php
// Turn on output buffering and error reporting
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require 'includes/database-connection.php';

// Set error message
$error = "";

// Get employee information
function getInfo(PDO $pdo, string $employeeID) {
    $sql = "SELECT employeeID, password, department FROM employee WHERE employeeID = :employeeID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['employeeID' => $employeeID]);
    return $stmt->fetch();
}

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeID = $_POST['employeeID'];
    $password = $_POST['password'];

    $info = getInfo($pdo, $employeeID);

    // Debug: Check what's returned from DB
    // var_dump($info); exit;

    if ($info && $password === $info['password']) {
        $_SESSION['employeeID'] = $employeeID;
        $_SESSION['department'] = $info['department'];

        // TEMPORARY: Debug echo to verify redirect logic
        echo "Login successful!<br>";
        echo "Redirecting to: " . ($info['department'] === 'Admin' ? 'admin.php' : 'basic.php');
        exit;

        // To enable real redirection, uncomment these lines and remove the above echo:
        /*
        if ($info['department'] === 'Admin') {
            header("Location: admin.php");
        } else {
            header("Location: basic.php");
        }
        exit;
        */
    } else {
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
    <title>Military Database</title>
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

<?php ob_end_flush(); ?>

