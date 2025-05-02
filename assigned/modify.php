<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['employeeID']) && isset($_GET['barcodeID'])) {
        #get values from url
        $og_employeeID = $_GET['employeeID'];
        $og_barcodeID = $_GET['barcodeID'];

        #get all other values (just due_date here)
        $sql_get = "SELECT *
                    FROM assigned_to
                    WHERE employeeID = :employeeID AND barcodeID = :barcodeID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'employeeID' => $og_employeeID,
            'barcodeID' => $og_barcodeID
        ]);

        $original = $get->fetch();

        #get values
        $og_dueDate = $original['due_date'];
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $employeeID = $_POST['employeeID'];
        $barcodeID = $_POST['barcodeID'];
        $due_date = $_POST['dueDate'];

        #update table
        $sql = "UPDATE assigned_to
                SET employeeID = :employeeID, barcodeID = :barcodeID, due_date = :due_date
                WHERE employeeID = :og_employeeID AND barcodeID = :og_barcodeID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'due_date' => $due_date,
            'og_employeeID' => $og_employeeID,
            'og_barcodeID' => $og_barcodeID
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get barcodes for dropdown
    $sql_barcodes = "SELECT b.barcodeID, i.name
                        FROM barcode b
                        JOIN item i ON i.itemID = b.itemID
                        WHERE b.status = 'available' OR b.barcodeID = :og_barcodeID";
    $get_barcodes = $pdo->prepare($sql_barcodes);
    $get_barcodes->execute(['og_barcodeID' => $og_barcodeID]);
    $barcodes = $get_barcodes->fetchAll();

    #get employees for dropdown
    $sql_employees = "SELECT e.employeeID, e.first_name, e.last_name
                        FROM employee e";
    $employees = $pdo->query($sql_employees)->fetchAll();

?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Modify Assignment</title>
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
            <!-- gets employeeID -->
            <label>Employee:
                <select name="employeeID" required>
                    <option value="">Select an Employee:</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= htmlspecialchars($employee['employeeID']) ?>" <?= $employee['employeeID'] == $og_employeeID ? 'selected' : '' ?>>
                            <?= htmlspecialchars($employee['first_name']) ?> <?= htmlspecialchars($employee['last_name']) ?> (ID: <?= htmlspecialchars($employee['employeeID']) ?>)
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
                            <?= htmlspecialchars($item['name']) ?> (ID: <?= htmlspecialchars($item['barcodeID']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets due date -->
            <label>Due Date:
                <input type="date" name="dueDate" value="<?= htmlspecialchars($og_dueDate) ?>" required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Modify Assignment</button>
        </form>
    </div>
</body>

</html>
