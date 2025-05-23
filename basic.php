<?php
    #start the session
    session_start();

    #initialize the connection
    include 'includes/database-connection.php';

    #get employeeID from login
    $employeeID = $_SESSION['employeeID'];

    #get available items
    $availableItemsQuery = "SELECT b.barcodeID, i.name, i.description, i.location_rack, i.location_shelf
                            FROM item i
                            JOIN barcode b ON i.itemID = b.itemID
                            WHERE b.status = 'available'";
    $availableItems = $pdo->query($availableItemsQuery)->fetchAll();

    #get issued items
    $issuedItemsQuery = "SELECT b.barcodeID, i.name, a.due_date, GREATEST(DATEDIFF(CURDATE(), a.due_date), 0) AS days_overdue
                         FROM item i
                         JOIN barcode b ON i.itemID = b.itemID
                         JOIN assigned_to a ON b.barcodeID = a.barcodeID
                         WHERE a.employeeID = :employeeID
                         ORDER BY days_overdue DESC";
    $issuedItems = $pdo->prepare($issuedItemsQuery);
    $issuedItems->execute(['employeeID' => $employeeID]);
    $issuedItems = $issuedItems->fetchAll();
    
?>

<!DOCTYPE html>
<html>

<!-- pulls from the basic.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Basic Dashboard</title>
    <link rel="stylesheet" href="css/basic.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Basic Dashboard</h1>
        <a href="logout.php">Log Out</a>
    </header>

    <div class = "dashboard-container">
        <div class = "dashboard-item">
            <!-- table of all available items (click an item, go to out transaction for it) -->
            <h2>Available Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barcode ID</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Rack</th>
                        <th>Shelf</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- checks if it's empty -->
                    <?php if (empty($availableItems)): ?>
                        <tr><td colspan = "5">No Available Items Found.</td></tr>
                    <!-- else, loop for each item -->
                    <?php else: ?>
                        <?php foreach ($availableItems as $item): ?>
                            <tr onclick="window.location.href='transaction.php?barcodeID=<?php echo $item['barcodeID']; ?>&transaction=out'">
                                <td> <?php echo htmlspecialchars($item['barcodeID']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['name']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['description']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['location_rack']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['location_shelf']); ?> </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class = "dashboard-item">
            <!-- table of all issued items (click an item, go to return transaction for it) -->
            <h2>Issued Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barcode ID</th>
                        <th>Item Name</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- checks if it's empty -->
                    <?php if (empty($issuedItems)): ?>
                        <tr><td colspan = "4">No Issued Items Found.</td></tr>
                    <!-- else, loop for each item -->
                    <?php else: ?>
                        <?php foreach ($issuedItems as $item): ?>
                            <tr onclick="window.location.href='transaction.php?barcodeID=<?php echo $item['barcodeID']; ?>&transaction=return'">
                                <td> <?php echo htmlspecialchars($item['barcodeID']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['name']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['due_date']); ?> </td>
                                <td> <?php echo htmlspecialchars($item['days_overdue']); ?> </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
