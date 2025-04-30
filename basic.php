<?php
    require_once 'includes/database-connection.php';
    
    // Initialize error message variable
    $error = "";
    $employeeID = "";
    $password = "";

    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $employeeID = $_POST['employeeID'];
        $password = $_POST['password'];

        // Verify credentials from the database
        $sql = "SELECT employeeID, password, department FROM employee WHERE employeeID = :employeeID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['employeeID' => $employeeID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the password matches
        if ($user && password_verify($password, $user['password'])) {
            // Valid login, display dashboard
            $employeeID = $user['employeeID'];
            $department = $user['department'];

            // If department is Basic, show the dashboard
            if ($department === 'Basic') {
                // Fetch available items for basic employees
                $availableItemsQuery = "SELECT name, location_rack, location_shelf FROM item INNER JOIN barcode ON item.itemID = barcode.itemID WHERE barcode.status = 'available'";
                $availableItems = $pdo->query($availableItemsQuery)->fetchAll(PDO::FETCH_ASSOC);

                // Fetch overdue items for basic employees
                $overdueItemsQuery = "SELECT item.name, assigned_to.due_date FROM item INNER JOIN barcode ON item.itemID = barcode.itemID INNER JOIN assigned_to ON barcode.barcodeID = assigned_to.barcodeID WHERE assigned_to.due_date < CURDATE()";
                $overdueItems = $pdo->query($overdueItemsQuery)->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = "Invalid login for Basic employee.";
            }
        } else {
            $error = "Invalid login credentials.";
        }
    }
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
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (!isset($employeeID)): ?>
                <!-- If the user isn't logged in, show the login form -->
                <h1>Login to Your Account</h1>
                <form method="POST" action="basic.php">
                    <div>
                        <label for="employeeID">Employee ID:</label>
                        <input type="text" id="employeeID" name="employeeID" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            <?php else: ?>
                <!-- Once logged in, show the basic dashboard -->
                <h1>Welcome, Employee #<?php echo htmlspecialchars($employeeID); ?></h1>

                <h2>Dashboard Options</h2>
                <ul>
                    <li><button onclick="toggleContent('available-items')">View Available Items</button></li>
                    <li><button onclick="toggleContent('overdue-items')">View Overdue Items</button></li>
                </ul>

                <!-- Available Items Section -->
                <div id="available-items" style="display:none;">
                    <h3>Available Items</h3>
                    <?php if (empty($availableItems)): ?>
                        <p>No available items found.</p>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>

                <!-- Overdue Items Section -->
                <div id="overdue-items" style="display:none;">
                    <h3>Overdue Items</h3>
                    <?php if (empty($overdueItems)): ?>
                        <p>No overdue items found.</p>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>

</html>
