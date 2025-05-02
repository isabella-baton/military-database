<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get the nextID
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(transactionID) AS max_id FROM transaction";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $employeeID = $_POST['employeeID'];
        $barcodeID = $_POST['barcodeID'];
        $transaction_type = $_POST['transaction_type'];
        $notes = $_POST['notes'];

        if ($notes === '') {
            $fullNote = "Admin Override";
        } else {
            $fullNote = "Admin Override: " . $notes;
        }
        
        #calculate transactionID
        $nextID = getNextID($pdo);

        #get current date for timestamp
        $timestamp = date('Y-m-d');

        #add to transaction table
        $sql = "INSERT INTO transaction(transactionID, employeeID, barcodeID, transaction_type, timestamp, notes)
                VALUES (:transactionID, :employeeID, :barcodeID, :transaction_type, :timestamp, :notes)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'transactionID' => $nextID,
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'transaction_type' => $transaction_type,
            'timestamp' => $timestamp,
            'notes' => $fullNote
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get barcodes for dropdown
    $sql_barcodes = "SELECT b.barcodeID, i.name
                     FROM barcode b
                     JOIN item i ON i.itemID = b.itemID";
    $barcodes = $pdo->query($sql_barcodes)->fetchAll();

    #get employees for dropdown
    $sql_employees = "SELECT e.employeeID, e.first_name, e.last_name
                      FROM employee e";
    $employees = $pdo->query($sql_employees)->fetchAll();

    $types = ["out", "return"];
?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Add Transaction</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Transaction</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets employeeID -->
            <label>Employee:
                <select name="employeeID" required>
                    <option value="">Select an Employee:</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= htmlspecialchars($employee['employeeID']) ?>">
                            <?=htmlspecialchars($employee['first_name'])?> <?=htmlspecialchars($employee['last_name'])?> (ID: <?= htmlspecialchars($employee['employeeID']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets barcodeID -->
            <label>Barcoded Item:
                <select name="barcodeID" required>
                    <option value="">Select an Item:</option>
                    <?php foreach ($barcodes as $item): ?>
                        <option value="<?= htmlspecialchars($item['barcodeID']) ?>">
                            <?=htmlspecialchars($item['name'])?> (ID: <?= htmlspecialchars($item['barcodeID']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets types -->
            <label>Type:
                <select name="transaction_type" required>
                    <option value="">Select a Type:</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>">
                            <?=htmlspecialchars($type)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets notes -->
            <label for="notes">Notes:
                <textarea name="notes" id="notes" rows="4" cols="50" maxlength="250"></textarea>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Add Transaction</button>
        </form>
    </div>
</body>

</html>
