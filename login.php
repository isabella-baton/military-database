<?php
	
	#database connection script !
	require 'includes/database-connection.php';

	#start the session
	session_start();

	#set error message to empty for later handling
	$error = "";


	#get employee information
	function getInfo(PDO $pdo, string $employeeID) {

		#sql query (gets only required fields)
		$sql = "SELECT employeeID, password, department				   
			FROM employee
			WHERE employeeID = :employeeID";

		#retrieve info using the sql query + pdo
		$info = pdo($pdo, $sql, ['employeeID' => $employeeID])->fetch();

		#return info
		return $info;
	}
	#check if request method is POST
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		#retrieve inputted info
		$employeeID = $_POST['employeeID'];
		$password = $_POST['password'];

		#retrieve employee info
		$info = getInfo($pdo, $employeeID, $password);

		#ensure there is info AND their password is correct
		if ($info && password_verify($password, $info['password'])) {
			#set the info for the session to the specifc employee
			$_SESSION['employeeID'] = $employeeID;
			$_SESSION['department'] = $info['department'];

			#redirect the employee to the correct place
			if ($info['department'] === 'Admin') {
				header("Location: admin.php");
			} else {
				header("Location: basic.php");
			}

			exit;
		} else {
			$error = "Invalid login - please try again.";
		}
		
	}
?> 

<!DOCTYPE>
<html>

	<head>
		<meta charset="UTF-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
  		<title>Military Database</title>
  		<link rel="stylesheet" href="css/style.css">
	</head>

	<body>
		<main>
			<div class="login-wrapper">
				<div class="login-container">
					<h1>Login</h1>

					<?php
					#checks if there is an error message
					if (empty($error) == false) {
						#outputs the error
						echo "<p class = 'error'>$error</p>";
					}
					?>

					<form action="login.php" method="POST">
						<div class="form-group">
							<label for="employeeID">Employee ID:</label>
							<input type="text" id="employeeID" name="employeeID" required>
						</div>

						<div class="form-group">
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" required>
						</div>

						<button type="submit">Submit Info</button>
					</form>
				</div>

			</div>

		</main>

	</body>

</html>
