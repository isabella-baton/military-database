<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['transactionID'])) {
        $transactionID = $_GET['transactionID'];

        $sql = "DELETE FROM transaction
                WHERE transactionID = :transactionID";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'transactionID' => $transactionID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>