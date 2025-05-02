<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['transactionID'])) {
        #get values from url
        $transactionID = $_GET['transactionID'];

        #get all other values
        $sql_get = "SELECT *
                    FROM transaction
                    WHERE transactionID = :transactionID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'transactionID' => $transactionID
        ]);

        $original = $get->fetch();

        #get values
        $og_employeeID = $original['employeeID'];
        $og_barcodeID = $original['barcodeID'];
        $og_type = $original['transaction_type'];
        $og_timestamp = $original['timestamp'];
        $og_notes = $original['notes'];
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $employeeID = $_POST['employeeID'];
        $barcodeID = $_POST['barcodeID'];
        $transaction_type = $_POST['transaction_type'];
        $timestamp = $_POST['timestamp'];
        $notes = $_POST['notes'];

        #update table
        $sql = "UPDATE transaction
                SET employeeID = :employeeID, barcodeID = :barcodeID, transaction_type = :transaction_type, timestamp = :timestamp, notes = :notes
                WHERE transactionID = :transactionID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'transaction_type' => $transaction_type,
            'timestamp' => $timestamp,
            'notes' => $notes,
            'transactionID' => $transactionID
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
    <title>Modify Transaction</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Modify Transaction</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets employeeID -->
            <label>Employee:
                <select name="employeeID" required>
                    <option value="">Select an Employee:</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= htmlspecialchars($employee['employeeID']) ?>" <?= $employee['employeeID'] == $og_employeeID ? 'selected' : '' ?>>
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
                        <option value="<?= htmlspecialchars($item['barcodeID']) ?>" <?= $item['barcodeID'] == $og_barcodeID ? 'selected' : '' ?>>
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
                        <option value="<?= htmlspecialchars($type) ?>" <?= $type == $og_type ? 'selected' : '' ?>>>
                            <?=htmlspecialchars($type)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets timestamp -->
            <label>Timestamp:
                <input type="date" name="timestamp" value="<?= htmlspecialchars($og_timestamp) ?>" required>
            </label> <br>

            <!-- gets notes -->
            <label for="notes">Notes:
                <textarea name="notes" id="notes" maxlength="250"
                onfocus="if(this.value=='<?= htmlspecialchars($og_notes) ?>')this.value='';"
                onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_notes) ?>';"
                required><?= htmlspecialchars($og_notes) ?></textarea>
            </label> <br>


            <!-- submits information to database -->
            <button type="submit">Modify Transaction</button>
        </form>
    </div>
</body>

</html>
