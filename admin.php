<?php
    #start the session
    session_start();

    #initialize the connection
    include 'includes/database-connection.php';

    #get employeeID from login
    $employeeID = $_SESSION['employeeID'];

    #get item info
    $itemQuery = "SELECT i.itemID, i.name, i.description, i.category, i.location_rack, i.location_shelf
                  FROM item i";
    $items = $pdo->query($itemQuery)->fetchAll();

    #get barcode info
    $barcodeQuery = "SELECT b.barcodeID, i.name, b.status
                  FROM barcode b
                  JOIN item i ON i.itemID = b.itemID";
    $barcodes = $pdo->query($barcodeQuery)->fetchAll();

    #get assigned item info
    $assignedItemsQuery = "SELECT b.barcodeID, i.name, e.employeeID, e.first_name, e.last_name, a.due_date, GREATEST(DATEDIFF(CURDATE(), a.due_date), 0) AS days_overdue
                           FROM assigned_to a
                           JOIN barcode b ON b.barcodeID = a.barcodeID
                           JOIN item i ON i.itemID = b.itemID
                           JOIN employee e ON a.employeeID = e.employeeID";
    $assignedItems = $pdo->query($assignedItemsQuery)->fetchAll();

    #get employee info
    $employeeQuery = "SELECT e.employeeID, e.first_name, e.last_name, e.department, e.role
                      FROM employee e";
    $employees = $pdo->query($employeeQuery)->fetchAll();

    #get supplier info
    $supplierQuery = "SELECT s.supplierID, s.name, c.address_building_number, c.address_street_name, c.address_city, c.address_state, c.address_zip, c.phone_number, c.email
                      FROM supplier s
                      JOIN contact_info c ON s.supplierID = c.supplierID";
    $suppliers = $pdo->query($supplierQuery)->fetchAll();

    #get transaction info
    $transactionQuery = "SELECT t.transactionID, b.barcodeID, i.name, e.employeeID, e.first_name, e.last_name, t.transaction_type, t.timestamp, t.notes
                         FROM transaction t
                         JOIN barcode b ON b.barcodeID = t.barcodeID
                         JOIN item i ON i.itemID = b.itemID
                         JOIN employee e ON e.employeeID = t.employeeID
                         ORDER BY t.transactionID DESC";
    $transactions = $pdo->query($transactionQuery)->fetchAll();                     
    
?>

<!DOCTYPE html>
<html>

<!-- pulls from the admin.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Log Out</a>
    </header>

    <div class = "dashboard-container">

        <!-- item table -->
        <div class = "dashboard-item">
            <h2>Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Rack</th>
                        <th>Shelf</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td> <?php echo htmlspecialchars($item['itemID']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['name']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['description']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['category']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['location_rack']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['location_shelf']); ?> </td>
                            <td>
                                <a href="item/modify.php?itemID=<?= $item['itemID'] ?>" class="modify-link">Modify</a>
                                <a href="item/delete.php?itemID=<?= $item['itemID'] ?>" class="delete-link" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='item/add.php'" class="button">Add Item</button>
        </div>

        <!-- barcode table -->
        <div class = "dashboard-item">
            <h2>Barcoded Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barcode ID</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($barcodes as $item): ?>
                        <!-- colors the rows ! -->
                        <tr class="<?php echo ($item['status'] === 'damaged') ? 'red' : (($item['status'] === 'issued') ? 'yellow' : ''); ?>">
                            <td> <?php echo htmlspecialchars($item['barcodeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['name']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['status']); ?> </td>
                            <td>
                                <a href="barcode/modify.php?barcodeID=<?= $item['barcodeID'] ?>" class="modify-link">Modify</a>
                                <a href="barcode/delete.php?barcodeID=<?= $item['barcodeID'] ?>" class="delete-link" onclick="return confirm('Delete this barcoded item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='barcode/add.php'" class="button">Add Barcoded Item</button>
        </div>
        
        <!-- assigned items table -->
        <div class = "dashboard-item">
            <h2>Assigned Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barcode ID</th>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($assignedItems as $item): ?>
                        <!-- colors the rows ! -->
                        <tr class="<?= ($item['days_overdue'] > 0) ? 'red' : '' ?>">
                            <td> <?php echo htmlspecialchars($item['barcodeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['name']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['employeeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['first_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['last_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['due_date']); ?> </td>
                            <td> <?php echo htmlspecialchars($item['days_overdue']); ?> </td>
                            <td>
                                <a href="assigned/modify.php?barcodeID=<?= $item['barcodeID'] ?>&employeeID=<?= $item['employeeID'] ?>" class="modify-link">Modify</a>
                                <a href="assigned/delete.php?barcodeID=<?= $item['barcodeID'] ?>&employeeID=<?= $item['employeeID'] ?>" class="delete-link" onclick="return confirm('Delete this assignment?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='assigned/add.php'" class="button">Add Item Assignment</button>
        </div>

        <!-- employee table -->
        <div class = "dashboard-item">
            <h2>Employees</h2>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Role</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td> <?php echo htmlspecialchars($employee['employeeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($employee['department']); ?> </td>
                            <td> <?php echo htmlspecialchars($employee['role']); ?> </td>
                            <td>
                                <a href="employee/modify.php?employeeID=<?= $employee['employeeID'] ?>" class="modify-link">Modify</a>
                                <a href="employee/delete.php?employeeID=<?= $employee['employeeID'] ?>" class="delete-link" onclick="return confirm('Delete this employee?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='employee/add.php'" class="button">Add Employee</button>
        </div>

        <!-- supplier table -->
        <div class = "dashboard-item">
            <h2>Suppliers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Supplier ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td> <?php echo htmlspecialchars($supplier['supplierID']); ?> </td>
                            <td> <?php echo htmlspecialchars($supplier['name']); ?> </td>
                            <td> <?php echo htmlspecialchars($supplier['address_building_number'] . ' ' . $supplier['address_street_name'] . ', ' . $supplier['address_city'] . ', ' . $supplier['address_state'] . ' ' . $supplier['address_zip']); ?> </td>
                            <td> <?php echo htmlspecialchars($supplier['phone_number']); ?> </td>
                            <td> <?php echo htmlspecialchars($supplier['email']); ?> </td>
                            <td>
                                <a href="supplier/modify.php?supplierID=<?= $supplier['supplierID'] ?>" class="modify-link">Modify</a>
                                <a href="supplier/delete.php?supplierID=<?= $supplier['supplierID'] ?>" class="delete-link" onclick="return confirm('Delete this supplier?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='supplier/add.php'" class="button">Add Supplier</button>
        </div>

        <!-- transaction table -->
        <div class = "dashboard-item">
            <h2>Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Barcode ID</th>
                        <th>Item Name</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Type</th>
                        <th>Timestamp</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <!-- colors the rows ! -->
                        <tr>
                            <td> <?php echo htmlspecialchars($transaction['transactionID']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['barcodeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['name']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['employeeID']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['transaction_type']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['timestamp']); ?> </td>
                            <td> <?php echo htmlspecialchars($transaction['notes']); ?> </td>
                            <td>
                                <a href="transaction/modify.php?employeeID=<?= $employee['employeeID'] ?>" class="modify-link">Modify</a>
                                <a href="transaction/delete.php?employeeID=<?= $employee['employeeID'] ?>" class="delete-link" onclick="return confirm('Delete this transaction?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='transaction/add.php'" class="button">Add Transaction</button>
        </div>


    </div>
</body>

</html>
