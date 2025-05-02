<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get the nextID
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(barcodeID) AS max_id FROM barcode";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $itemID = $_POST['itemID'];
        
        #calculate barcodeID
        $nextID = getNextID($pdo);

        #add to table
        $sql = "INSERT INTO barcode(barcodeID, itemID, status)
                VALUES (:barcodeID, :itemID, 'available')";
        $add = $pdo->prepare($sql);
        $add->execute([
            'barcodeID' => $nextID,
            'itemID' => $itemID
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get items for dropdown
    $sql_items = "SELECT i.itemID, i.name, i.description
                  FROM item i";
    $items = $pdo->query($sql_items)->fetchAll();

?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Add Barcoded Item</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Barcoded Item</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets itemID -->
            <label>Item:
                <select name="itemID" required>
                    <option value="">Select an Item:</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= htmlspecialchars($item['itemID']) ?>">
                            <?=htmlspecialchars($item['name'])?> - <?=htmlspecialchars($item['description'])?> (ID: <?= htmlspecialchars($item['itemID']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Add Barcoded Item</button>
        </form>
    </div>
</body>

</html>
