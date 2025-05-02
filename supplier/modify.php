<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['supplierID'])) {
        #get values from url
        $supplierID = $_GET['supplierID'];

        #get all other values
        $sql_get = "SELECT *
                    FROM supplier s
                    JOIN contact_info c ON s.supplierID = c.supplierID
                    WHERE s.supplierID = :supplierID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'supplierID' => $supplierID
        ]);

        $original = $get->fetch();

        #get values
        $og_name = $original['name'];
        $og_address_building_number = $original['address_building_number'];
        $og_address_street_name = $original['address_street_name'];
        $og_address_city = $original['address_city'];
        $og_address_state = $original['address_state'];
        $og_address_zip = $original['address_zip'];
        $og_phone_number = $original['phone_number'];
        $og_email = $original['email'];
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

        #update supplier table
        $sql = "UPDATE supplier
                SET name = :name
                WHERE supplierID = :supplierID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'name' => $name,
            'supplierID' => $supplierID
        ]);

        #add to contact_info table
        $sql = "UPDATE contact_info
                SET address_building_number = :address_building_number, address_street_name = :address_street_name, address_city = :address_city, address_state = :address_state, address_zip = :address_zip, phone_number = :phone_number, email = :email
                WHERE supplierID = :supplierID";
        $add = $pdo->prepare($sql);
        $add->execute([
            'address_building_number' => $address_building_number,
            'address_street_name' => $address_street_name,
            'address_city' => $address_city,
            'address_state' => $address_state,
            'address_zip' => $address_zip,
            'phone_number' => $phone_number,
            'email' => $email,
            'supplierID' => $supplierID
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
    <title>Modify Supplier</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Modify Supplier</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets name -->
            <label for="name">Name:
                <input type="text" name="name" id="name" maxlength="50" 
                       value="<?= htmlspecialchars($og_name) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_name) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_name) ?>';"
                       required>
            </label> <br>

            <!-- gets building num -->
            <label for="address_building_number">Building Number:
                <input type="number" name="address_building_number" id="address_building_number" min="0" max="9999" step="1" 
                       value="<?= htmlspecialchars($og_address_building_number) ?>"
                       data-original="<?= htmlspecialchars($og_address_building_number) ?>"
                       required>
            </label> <br>

            <!--js script for the focus/blur bc its numeric and u cant use text focus/blur :( -->
                <script>
                const numInput = document.getElementById('address_building_number');
                const originalValue_num = numInput.dataset.original;

                numInput.addEventListener('focus', () => {
                    if (numInput.value === originalValue_num) {
                        numInput.value = '';
                    }
                });

                numInput.addEventListener('blur', () => {
                    if (numInput.value === '') {
                        numInput.value = originalValue_num;
                    }
                });
            </script>

            <!-- gets street -->
            <label for="address_street_name">Street Name:
                <input type="text" name="address_street_name" id="address_street_name" maxlength="50" 
                       value="<?= htmlspecialchars($og_address_street_name) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_address_street_name) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_address_street_name) ?>';"
                       required>
            </label> <br>

           <!-- gets city -->
           <label for="address_city">City:
                <input type="text" name="address_city" id="address_city" maxlength="25" 
                       value="<?= htmlspecialchars($og_address_city) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_address_city) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_address_city) ?>';"
                       required>
            </label> <br>

            <!-- gets state -->
            <label for="address_state">State:
                <input type="text" name="address_state" id="address_state" maxlength="2" 
                       value="<?= htmlspecialchars($og_address_state) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_address_state) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_address_state) ?>';"
                       required>
            </label> <br>

            <!-- gets zip -->
            <label for="address_zip">Zip:
                <input type="text" name="address_zip" id="address_zip" maxlength="50" min="10000" max="99999" step="1" 
                       value="<?= htmlspecialchars($og_address_zip) ?>"
                       data-original="<?= htmlspecialchars($og_address_zip) ?>"
                       required>
            </label> <br>

            <!--js script for the focus/blur bc its numeric and u cant use text focus/blur :( -->
                <script>
                const zipInput = document.getElementById('address_zip');
                const originalValue_zip = zipInput.dataset.original;

                zipInput.addEventListener('focus', () => {
                    if (zipInput.value === originalValue_zip) {
                        zipInput.value = '';
                    }
                });

                zipInput.addEventListener('blur', () => {
                    if (zipInput.value === '') {
                        zipInput.value = originalValue_zip;
                    }
                });
            </script>

            <!-- gets phone number -->
            <label for="phone_number">Phone Number:
                <input type="text" name="phone_number" id="phone_number" maxlength="12" 
                       value="<?= htmlspecialchars($og_phone_number) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_phone_number) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_phone_number) ?>';"
                       required>
            </label> <br>

            <!-- gets email -->
            <label for="email">Email:
                <input type="email" name="email" id="email" maxlength="50" 
                       value="<?= htmlspecialchars($og_email) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_email) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_email) ?>';"
                       required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Modify Supplier</button>
        </form>
    </div>
</body>

</html>