<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get the nextID
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(supplierID) AS max_id FROM supplier";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $name = $_POST['name'];
        $address_building_number = $_POST['address_building_number'];
        $address_street_name = $_POST['address_street_name'];
        $address_city = $_POST['address_city'];
        $address_state = $_POST['address_state'];
        $address_zip = $_POST['address_zip'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        
        #calculate supplierID
        $nextID = getNextID($pdo);

        #add to supplier table
        $sql = "INSERT INTO supplier(supplierID, name)
                VALUES (:supplierID, :name)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'supplierID' => $nextID,
            'name' => $name
        ]);

        #add to contact_info table
        $sql = "INSERT INTO contact_info(supplierID, address_building_number, address_street_name, address_city, address_state, address_zip, phone_number, email)
                VALUES (:supplierID, :address_building_number, :address_street_name, :address_city, :address_state, :address_zip, :phone_number, :email)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'supplierID' => $nextID,
            'address_building_number' => $address_building_number,
            'address_street_name' => $address_street_name,
            'address_city' => $address_city,
            'address_state' => $address_state,
            'address_zip' => $address_zip,
            'phone_number' => $phone_number,
            'email' => $email
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }
?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Supplier</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets name -->
            <label for="name">Name:
                <input type="text" name="name" id="name" maxlength="50" required>
            </label> <br>

            <!-- gets building num -->
            <label for="address_building_number">Building Number:
                <input type="number" name="address_building_number" id="address_building_number" min="0" max="9999" step="1" required>
            </label> <br>

            <!-- gets street -->
            <label for="address_street_name">Street Name:
                <input type="text" name="address_street_name" id="address_street_name" maxlength="50" required>
            </label> <br>

           <!-- gets city -->
           <label for="address_city">City:
                <input type="text" name="address_city" id="address_city" maxlength="25" required>
            </label> <br>

            <!-- gets state -->
            <label for="address_state">State:
                <input type="text" name="address_state" id="address_state" maxlength="2" required>
            </label> <br>

            <!-- gets zip -->
            <label for="address_zip">Zip:
                <input type="text" name="address_zip" id="address_zip" maxlength="50" min="10000" max="99999" step="1" required>
            </label> <br>

            <!-- gets phone number -->
            <label for="phone_number">Phone Number:
                <input type="text" name="phone_number" id="phone_number" maxlength="12" required>
            </label> <br>

            <!-- gets email -->
            <label for="email">Email:
                <input type="email" name="email" id="email" maxlength="50" required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Add Supplier</button>
        </form>
    </div>
</body>

</html>
