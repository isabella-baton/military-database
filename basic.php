<?php
    require_once 'includes/database-connection.php';
    session_start();
    
    // Check if the user is logged in and session data exists
    if (!isset($_SESSION['employeeID'])) {
        header("Location: login.php");
        exit;
    }

    // Fetch user details
    $employeeID = $_SESSION['employeeID'];
    $department = $_SESSION['department'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Military Database - Basic Dashboard</title>
    <link rel="stylesheet" href="css/basic.css">
    <script>
        // Function to toggle visibility of content
        function toggleContent(contentId) {
            var content = document.getElementById(contentId);
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>

<body>

    <header>
        <nav>
            <ul>
                <li><a href="login.php">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="dashboard-container">
            <h1>Welcome, Employee #<?php echo htmlspecialchars($employeeID); ?></h1>
            <p>Department: <?php echo htmlspecialchars($department); ?></p>

            <h2>Dashboard Options</h2>
            <ul>
                <li><button onclick="toggleContent('available-items')">View Available Items</button></li>
                <li><button onclick="toggleContent('overdue-items')">View Overdue Items</button></li>
            </ul>

            <!-- Available Items Section -->
            <div id="available-items" style="display:none;">
                <h3>Available Items</h3>
                <?php
                    $availableItemsQuery = "SELECT name, location_rack, location_shelf FROM item INNER JOIN barcode ON item.itemID = barcode.itemID WHERE barcode.status = 'available'";
                    $availableItems = $pdo->query($availableItemsQuery)->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Rack Location</th>
                            <th>Shelf Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['location_rack']); ?></td>
                                <td><?php echo htmlspecialchars($item['location_shelf']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Overdue Items Section -->
            <div id="overdue-items" style="display:none;">
                <h3>Overdue Items</h3>
                <?php
                    $overdueItemsQuery = "SELECT item.name, assigned_to.due_date FROM item INNER JOIN barcode ON item.itemID = barcode.itemID INNER JOIN assigned_to ON barcode.barcodeID = assigned_to.barcodeID WHERE assigned_to.due_date < CURDATE()";
                    $overdueItems = $pdo->query($overdueItemsQuery)->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdueItems as $overdue): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($overdue['name']); ?></td>
                                <td><?php echo htmlspecialchars($overdue['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</body>

</html>