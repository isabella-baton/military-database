<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['barcodeID'])) {
        #get values from url
        $barcodeID = $_GET['barcodeID'];

        #get all other values
        $sql_get = "SELECT *
                    FROM barcode
                    WHERE barcodeID = :barcodeID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'barcodeID' => $barcodeID
        ]);

        $original = $get->fetch();

        #get values
        $og_itemID = $original['itemID'];
        $og_status = $original['status'];
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $itemID = $_POST['itemID'];
        $status = $_POST['status'];

        #update table
        $sql = "UPDATE barcode
                SET itemID = :itemID, status = :status
                WHERE barcodeID = :barcodeID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'itemID' => $itemID,
            'status' => $status,
            'barcodeID' => $barcodeID
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get items for dropdown
    $sql_items = "SELECT i.itemID, i.name, i.description
                  FROM item i";
    $items = $pdo->query($sql_items)->fetchAll();

    #get statuses for dropdown
    $statuses = ["available", "issued", "damaged"];

?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Modify Barcoded Item</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Modify Assignment</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets itemID -->
            <label>Item:
                <select name="itemID" required>
                    <option value="">Select an Item:</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= htmlspecialchars($item['itemID']) ?>" <?= $item['itemID'] == $og_itemID ? 'selected' : '' ?>>
                            <?=htmlspecialchars($item['name'])?> - <?=htmlspecialchars($item['description'])?> (ID: <?= htmlspecialchars($item['itemID']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets status -->
            <label>Status:
                <select name="status" required>
                    <option value="">Select a Status:</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= $status == $og_status ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Modify Barcoded Item</button>
        </form>
    </div>
</body>

</html>
