<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['supplierID'])) {
        $supplierID = $_GET['supplierID'];

        $sql = "DELETE FROM supplier
                WHERE supplierID = :supplierID";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'supplierID' => $supplierID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>