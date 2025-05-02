<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    if (isset($_GET['itemID'])) {
        $itemID = $_GET['itemID'];

        $sql = "DELETE FROM item
                WHERE itemID = :itemID ";

        $delete = $pdo->prepare($sql);
        $delete->execute([
            'itemID' => $itemID
        ]);
    }

    #send back to dashboard immediately
    header("Location: ../admin.php");
    exit;

?>