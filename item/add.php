<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get the nextID
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(itemID) AS max_id FROM item";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $supplierID = $_POST['supplierID'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $location_rack = $_POST['location_rack'];
        $location_shelf = $_POST['location_shelf'];
        $max_checkout_days = $_POST['max_checkout_days'];
        
        #calculate itemID
        $nextID = getNextID($pdo);

        #add to table
        $sql = "INSERT INTO item(itemID, supplierID, name, description, category, location_rack, location_shelf, max_checkout_days)
                VALUES (:itemID, :supplierID, :name, :description, :category, :location_rack, :location_shelf, :max_checkout_days)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'itemID' => $nextID,
            'supplierID' => $supplierID,
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'location_rack' => $location_rack,
            'location_shelf' => $location_shelf,
            'max_checkout_days' => $max_checkout_days
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get suppliers for dropdown
    $sql_supplier = "SELECT s.supplierID, s.name
                  FROM supplier s";
    $suppliers = $pdo->query($sql_supplier)->fetchAll();

    #get categories for dropdown
    $categories = ["Comm", "Support", "Network"];
?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Add Item</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Item</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets supplier -->
            <label>Supplier:
                <select name="supplierID" required>
                    <option value="">Select a Supplier:</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= htmlspecialchars($supplier['supplierID']) ?>">
                        <?=htmlspecialchars($supplier['name'])?> (ID: <?= htmlspecialchars($supplier['supplierID']) ?>)
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets name -->
            <label for="name">Name:
                <input type="text" name="name" id="name" maxlength="25" required>
            </label> <br>

            <!-- gets description -->
            <label for="description">Description:
                <input type="text" name="description" id="description" maxlength="25" required>
            </label> <br>

            <!-- gets category -->
            <label>Category:
                <select name="category" required>
                    <option value="">Select a Category:</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>">
                            <?=htmlspecialchars($category)?>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets rack -->
            <label for="location_rack">Rack:
                <input type="number" name="location_rack" id="location_rack" min="0" max="999" step="1" required>
            </label> <br>

            <!-- gets shelf -->
            <label for="location_shelf">Shelf:
                <input type="number" name="location_shelf" id="location_shelf" min="0" max="999" step="1" required>
            </label> <br>

           <!-- gets checkout days -->
           <label for="max_checkout_days">Max Checkout Days:
                <input type="number" name="max_checkout_days" id="max_checkout_days" min="0" max="999" step="1" required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Add Item</button>
        </form>
    </div>
</body>

</html>
