<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['employeeID']) && isset($_GET['barcodeID'])) {
        $employeeID = $_GET['employeeID'];
        $barcodeID = $_GET['barcodeID'];

        $sql = "DELETE FROM assigned_to
                WHERE employeeID = :employeeID AND barcodeID = :barcodeID";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>