<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['employeeID'])) {
        $employeeID = $_GET['employeeID'];

        $sql = "DELETE FROM employee
                WHERE employeeID = :employeeID ";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'employeeID' => $employeeID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>