<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['barcodeID'])) {
        $barcodeID = $_GET['barcodeID'];

        $sql = "DELETE FROM barcode
                WHERE barcodeID = :barcodeID ";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'barcodeID' => $barcodeID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>