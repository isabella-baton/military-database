<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get original values
    if(isset($_GET['employeeID'])) {
        #get values from url
        $employeeID = $_GET['employeeID'];

        #get all other values
        $sql_get = "SELECT *
                    FROM employee
                    WHERE employeeID = :employeeID";

        $get = $pdo->prepare($sql_get);
        $get->execute([
            'employeeID' => $employeeID
        ]);

        $original = $get->fetch();

        #get values
        $og_password = $original['password'];
        $og_first_name = $original['first_name'];
        $og_last_name = $original['last_name'];
        $og_department = $original['department'];
        $og_role = $original['role'];
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $department = $_POST['department'];
        $role = $_POST['role'];

        #update table
        $sql = "UPDATE employee
                SET password = :password, first_name = :first_name, last_name = :last_name, department = :department, role = :role
                WHERE employeeID = :employeeID";
        $update = $pdo->prepare($sql);
        $update->execute([
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'department' => $department,
            'role' => $role,
            'employeeID' => $employeeID
        ]);

        #send back to dashboard after completion
        header("Location: ../admin.php");
        exit;

    }

    #get departments for dropdown
    $departments = ["IT", "Medical", "Security", "Training", "Comm", "Admin"];
?>

<!DOCTYPE html>
<html>

<!-- pulls from the add_modify.css file :) -->
<head>
    <meta charset="UTF-8">
    <title>Modify Barcoded Item</title>
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
            <!-- gets password -->
            <label for="password">Password:
                <input type="text" name="password" id="password" maxlength="10" 
                       value="<?= htmlspecialchars($og_password) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_password) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_password) ?>';"
                       required>
            </label> <br>

            <!-- gets first name -->
            <label for="first_name">First Name:
                <input type="text" name="first_name" id="first_name" maxlength="25" 
                       value="<?= htmlspecialchars($og_first_name) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_first_name) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_first_name) ?>';"
                       required>
            </label> <br>

            <!-- gets last name -->
            <label for="last_name">Last Name:
                <input type="text" name="last_name" id="last_name" maxlength="25" 
                       value="<?= htmlspecialchars($og_last_name) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_last_name) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_last_name) ?>';"
                       required>
            </label> <br>

            <!-- gets department -->
            <label>Department:
                <select name="department" required>
                    <option value="">Select a Department:</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department) ?>" <?= $department == $og_department ? 'selected' : '' ?>>
                            <?=htmlspecialchars($department)?>
                    <?php endforeach; ?>
                </select>
            </label> <br>

           <!-- gets role -->
           <label for="role">Role:
                <input type="text" name="role" id="role" maxlength="50" 
                       value="<?= htmlspecialchars($og_role) ?>"
                       onfocus="if(this.value=='<?= htmlspecialchars($og_role) ?>')this.value='';"
                       onblur="if(this.value=='') this.value='<?= htmlspecialchars($og_role) ?>';"
                       required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Modify Employee</button>
        </form>
    </div>
</body>

</html>
