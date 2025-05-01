<?php
    include "includes/database-connection.php";
    
    #get the parameters
    $barcodeID = $_GET["barcodeID"];
    $employeeID = $_GET["employeeID"];
    $transaction_type = $_GET["transaction"];

    #get the item"s information
    function getInfo(PDO $pdo, string $barcodeID) {
        $sql = "SELECT b.barcodeID, i.name, i.description, i.max_checkout_days
                FROM item i
                JOIN barcode b ON i.itemID = b.itemID
                WHERE b.barcodeID = :barcodeID";
        $item = pdo($pdo, $sql, ["barcodeID" => $barcodeID])->fetch();
        return $item;
    }

    $item = getInfo($pdo, $barcodeID);

    #get the next id for a new transaction
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(transactionID) AS max_id FROM transaction";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #insert transaction function (for input handling !)
    function insertTransaction(PDO $pdo, string $barcodeID, string $employeeID, string $transaction_type, string $notes) {
        #set the notes to the correct value
        if ($notes === "") {
            $notes = NULL;
        }

        #get the next id to use
        $nextID = getNextID($pdo);

        #sql query
        $sql = "INSERT INTO transaction(transactionID, employeeID, barcodeID, transaction_type, timestamp, notes)
                VALUES (:transactionID, :employeeID, :barcodeID, :transaction_type, :notes, :timestamp)";
        
        #add to table
        $add = $pdo->prepare($sql);
        $add->execute([
            'transactionID' => $nextID,
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'transaction_type' => $transaction_type,
            'timestamp' => date('Y-m-d'),
            'notes' => $notes
        ]);
    }

    #insert assigned_to function (for input handling !)
    function addAssignment(PDO $pdo, string $barcodeID, string $employeeID) {
        #sql query
        $sql = "INSERT INTO assigned_to(employeeID, barcodeID, due_date)
                VALUES (:employeeID, :barcodeID, :due_date)";
        
        #get the item's information
        $item = getInfo($pdo, $barcodeID);

        #get due date
        $dueDate = new DateTime(); #today
        $dueDate->modify("+{$item['max_checkout_days']} days"); #due date

        #add to table
        $add = $pdo->prepare($sql);
        $add->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'due_date' => $dueDate->format('Y-m-d')
        ]);
    }

    #delete assigned_to function (for input handling !)
    function deleteAssignment(PDO $pdo, string $barcodeID, string $employeeID) {
        #sql query
        $sql = "DELETE FROM assigned_to
                WHERE employeeID = :employeeID AND barcodeID = :barcodeID";
        
        #remove from table
        $remove = $pdo->prepare($sql);
        $remove->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID
        ]);
    }

    #form submission logic
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $notes = $_POST['notes'];
        #add transaction to transaction table
        insertTransaction($pdo, $barcodeID, $employeeID, $transaction_type, $notes);

        if ($transaction_type === "return") {
            #removes from assigned table
            deleteAssignment($pdo, $barcodeID, $employeeID);
        } else if ($transaction_type === "out") {
            #adds to assigned table
            addAssignment($pdo, $barcodeID, $employeeID);
        }
    }
?>

<!DOCTYPE html>
<html>

<!-- pulls from the transaction.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Transaction Dashboard</title>
    <link rel="stylesheet" href="css/transaction.css">
</head>

<body>
    <!-- header prints the name of the dashboard -->
    <header>
        <h1>Transaction</h1>
        <a href="basic.php">Back to Dashboard</a>
    </header>

    <div class = "transaction-container">
        <!-- prints the details of the item clicked -->
        <div class="item-details">
            <h2>Item Details</h2>
            <p><strong>Barcode ID:</strong> <?= htmlspecialchars($item['barcodeID']) ?></p>
            <p><strong>Item Name:</strong> <?= htmlspecialchars($item['name']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
        </div>

        <!-- prints the transaction form with input -->
        <div class="transaction-form">
            <form method="POST">
                <!-- changes label based on the return type -->
                <label for="notes">
                    <?= $transaction_type === "return" ? "Reason for Return (required):" : "Notes (optional):" ?>
                </label>

                <!-- makes input to the text area require if a return -->
                <textarea id="notes" name="notes" <?= $transaction_type === "return" ? "required" : "" ?>></textarea>
                <input type="submit" value="Submit!">
            </form>
        </div>
    </div>
</body>

</html>
