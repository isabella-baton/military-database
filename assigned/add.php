<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $employeeID = $_POST['employeeID'];
        $barcodeID = $_POST['barcodeID'];
        
        #calculate due date
        $sql_dueDate = "SELECT i.max_checkout_days
                        FROM barcode b
                        JOIN item i ON b.itemID = i.itemID
                        WHERE b.barcodeID = :barcodeID";
        $maxDays = $pdo->prepare($sql_dueDate);
        $maxDays->execute(['barcodeID' => $barcodeID]);
        $maxDays = $maxDays->fetch();

        if (!$maxDays) {
            die("Invalid barcode ID or item not found.");
        }

        $dueDate = new DateTime();
        $dueDate->modify("+{$maxDays['max_checkout_days']} days");
        $dueDateFormatted = $dueDate->format('Y-m-d');


        #add to table
        $sql = "INSERT INTO assigned_to(employeeID, barcodeID, due_date)
                VALUES (:employeeID, :barcodeID, :due_date)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'employeeID' => $employeeID,
            'barcodeID' => $barcodeID,
            'due_date' => $dueDateFormatted
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get barcodes for dropdown
    $sql_barcodes = "SELECT b.barcodeID, i.name
                        FROM barcode b
                        JOIN item i ON i.itemID = b.itemID
                        WHERE b.status = 'available'";
    $barcodes = $pdo->query($sql_barcodes)->fetchAll();

    #get employees for dropdown
    $sql_employees = "SELECT e.employeeID, e.first_name, e.last_name
                        FROM employee e";
    $employees = $pdo->query($sql_employees)->fetchAll();

?>

<!DOCTYPE html>
<html>

<!-- pulls from the admin.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Add Assignment</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Assignment</h1>
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

            <!-- submits information to database -->
            <button type="submit">Add Assignment</button>
        </form>
    </div>
</body>

</html>
