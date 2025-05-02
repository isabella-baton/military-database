<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['itemID'])) {
        #get values from url
        $itemID = $_GET['itemID'];

        #get all other values
        $sql_get = "SELECT *
                    FROM item
                    WHERE itemID = :itemID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'itemID' => $itemID
        ]);

        $original = $get->fetch();

        #get values
        $og_supplierID = $original['supplierID'];
        $og_name = $original['name'];
        $og_description = $original['description'];
        $og_category = $original['category'];
        $og_location_rack = $original['location_rack'];
        $og_location_shelf = $original['location_shelf'];
        $og_max_checkout_days = $original['max_checkout_days'];
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

        #update table
        $sql = "UPDATE item
                SET supplierID = :supplierID, name = :name, description = :description, category = :category, location_rack = :location_rack, location_shelf = :location_shelf, max_checkout_days = :max_checkout_days
                WHERE itemID = :itemID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'supplierID' => $supplierID,
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'location_rack' => $location_rack,
            'location_shelf' => $location_shelf,
            'max_checkout_days' => $max_checkout_days,
            'itemID' => $itemID
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
    <title>Modify Item</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Modify an Item</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets supplier -->
            <label>Supplier:
                <select name="supplierID" required>
                    <option value="">Select a Supplier:</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= htmlspecialchars($supplier['supplierID']) ?>" <?= $supplier['supplierID'] == $og_supplierID ? 'selected' : '' ?>>
                        <?=htmlspecialchars($supplier['name'])?> (ID: <?= htmlspecialchars($supplier['supplierID']) ?>)
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets name -->
            <label for="name">Name:
                <input type="text" name="name" id="name" maxlength="25" 
                       value="<?= htmlspecialchars($og_name) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_name) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_name) ?>';"
                       required>
            </label> <br>

            <!-- gets description -->
            <label for="description">Description:
                <input type="text" name="description" id="description" maxlength="25" 
                       value="<?= htmlspecialchars($og_description) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_description) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_description) ?>';"
                       required>
            </label> <br>

            <!-- gets category -->
            <label>Category:
                <select name="category" required>
                    <option value="">Select a Category:</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>" <?= $category == $og_category ? 'selected' : '' ?>>
                            <?=htmlspecialchars($category)?>
                    <?php endforeach; ?>
                </select>
            </label> <br>

            <!-- gets rack -->
            <label for="location_rack">Rack:
                <input type="number" name="location_rack" id="location_rack" min="0" max="999" step="1" 
                       value="<?= htmlspecialchars($og_location_rack) ?>"
                       data-original="<?= htmlspecialchars($og_location_rack) ?>"
                       required>
            </label> <br>

            <!--js script for the focus/blur bc its numeric and u cant use text focus/blur :( -->
            <script>
                const rackInput = document.getElementById('location_rack');
                const originalValue_rack = rackInput.dataset.original;

                rackInput.addEventListener('focus', () => {
                    if (rackInput.value === originalValue_rack) {
                        rackInput.value = '';
                    }
                });

                rackInput.addEventListener('blur', () => {
                    if (rackInput.value === '') {
                        rackInput.value = originalValue_rack;
                    }
                });
            </script>

            <!-- gets shelf -->
            <label for="location_shelf">Shelf:
                <input type="number" name="location_shelf" id="location_shelf" min="0" max="999" step="1" 
                       value="<?= htmlspecialchars($og_location_shelf) ?>"
                       data-original="<?= htmlspecialchars($og_location_shelf) ?>"
                       required>
            </label> <br>

            <!--js script for the focus/blur bc its numeric and u cant use text focus/blur :( -->
            <script>
                const shelfInput = document.getElementById('location_shelf');
                const originalValue_shelf = shelfInput.dataset.original;

                shelfInput.addEventListener('focus', () => {
                    if (shelfInput.value === originalValue_shelf) {
                        shelfInput.value = '';
                    }
                });

                shelfInput.addEventListener('blur', () => {
                    if (shelfInput.value === '') {
                        shelfInput.value = originalValue_shelf;
                    }
                });
            </script>

            <!-- gets rack -->
            <label for="max_checkout_days">Max Checkout Days:
                <input type="number" name="max_checkout_days" id="max_checkout_days" min="0" max="999" step="1" 
                       value="<?= htmlspecialchars($og_max_checkout_days) ?>"
                       data-original="<?= htmlspecialchars($og_max_checkout_days) ?>"
                       required>
            </label> <br>

            <!--js script for the focus/blur bc its numeric and u cant use text focus/blur :( -->
            <script>
                const daysInput = document.getElementById('max_checkout_days');
                const originalValue_max = daysInput.dataset.original;

                daysInput.addEventListener('focus', () => {
                    if (daysInput.value === originalValue_max) {
                        daysInput.value = '';
                    }
                });

                daysInput.addEventListener('blur', () => {
                    if (daysInput.value === '') {
                        daysInput.value = originalValue_max;
                    }
                });
            </script>

            <!-- submits information to database -->
            <button type="submit">Modify Item</button>
        </form>
    </div>
</body>

</html>