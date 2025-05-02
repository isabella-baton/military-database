<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #get the nextID
    function getNextID(PDO $pdo) {
        #finds the max id
        $sql = "SELECT MAX(employeeID) AS max_id FROM employee";

        #gets the max id
        $result = $pdo->query($sql)->fetch();

        #adds one (gets next max id) and returns it
        return $result['max_id'] + 1;
    }

    #form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        #get submitted values
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $department = $_POST['department'];
        $role = $_POST['role'];
        
        #calculate barcodeID
        $nextID = getNextID($pdo);

        #add to table
        $sql = "INSERT INTO employee(employeeID, password, first_name, last_name, department, role)
                VALUES (:employeeID, :password, :first_name, :last_name, :department, :role)";
        $add = $pdo->prepare($sql);
        $add->execute([
            'employeeID' => $nextID,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'department' => $department,
            'role' => $role
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
    <title>Add Employee</title>
    <link rel="stylesheet" href="../css/add_modify.css">
</head>

<body>
    <!-- header prints the name of the page and has a link back to the login -->
    <header>
        <h1>Create new Employee</h1>
        <a href="../admin.php">Return to Dashboard</a>
    </header>
    <div class="add_modify-container">
        <form class="add_modify-form" method="POST">
            <!-- gets password -->
            <label for="password">Password:
                <input type="text" name="password" id="password" maxlength="10" required>
            </label> <br>

            <!-- gets first name -->
            <label for="first_name">First Name:
                <input type="text" name="first_name" id="first_name" maxlength="25" required>
            </label> <br>

            <!-- gets last name -->
            <label for="last_name">Last Name:
                <input type="text" name="last_name" id="last_name" maxlength="25" required>
            </label> <br>

            <!-- gets department -->
            <label>Department:
                <select name="department" required>
                    <option value="">Select a Department:</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department) ?>">
                            <?=htmlspecialchars($department)?>
                    <?php endforeach; ?>
                </select>
            </label> <br>

           <!-- gets role -->
           <label for="role">Role:
                <input type="text" name="role" id="role" maxlength="50" required>
            </label> <br>

            <!-- submits information to database -->
            <button type="submit">Add Employee</button>
        </form>
    </div>
</body>

</html>
