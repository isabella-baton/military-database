<?php
ob_start();
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'includes/database-connection.php';

function getInfo(PDO $pdo, string $employeeID) {
    $sql = "SELECT employeeID, password, department FROM employee WHERE employeeID = :employeeID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['employeeID' => $employeeID]);
    return $stmt->fetch();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employeeID = $_POST['employeeID'];
    $password = $_POST['password'];

    $info = getInfo($pdo, $employeeID);

    if ($info && $password === $info['password']) {
        $_SESSION['employeeID'] = $employeeID;
        $_SESSION['department'] = $info['department'];

        // DEBUG: print instead of redirect
        echo "<h2>Login successful!</h2>";
        echo "<p>Department: {$info['department']}</p>";
        echo "<p>Redirecting to: " . ($info['department'] === 'Admin' ? 'admin.php' : 'basic.php') . "</p>";

        // To test redirect uncomment below and comment out the echo above:
        // header("Location: " . ($info['department'] === 'Admin' ? 'admin.php' : 'basic.php'));
        exit;
    } else {
        echo "<h2>Invalid login - please try again.</h2>";
    }
}
?>

<form method="POST" action="login.php">
    <label>Employee ID: <input type="text" name="employeeID" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>

