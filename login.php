<?php   										// Opening PHP tag
	
	// Include the database connection script
	require 'includes/database-connection.php';



	 function getInfo(PDO $pdo, string $employeeID, string $password) {

		//sql query (getting all so i can use as)
		$sql = "SELECT * 				   
			FROM employee e
			WHERE e.employeeID = :employeeID
			AND e.password = :password;";

		#get the info using pdo
		$info = pdo($pdo, $sql, ['employeeID' => $employeeID, 'password' => $password])->fetch();

		//return info
		return $info;
	}

	
	// Check if the request method is POST (i.e, form submitted)
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		// Retrieve the value of the 'email' field from the POST data
		$email = $_POST['email'];

		// Retrieve the value of the 'orderNum' field from the POST data
		$password = $_POST['password'];

		//retrieve info
		$info = getInfo($pdo, $email, $orderNum);
		
	}
// Closing PHP tag  ?> 

<!DOCTYPE>
<html>

	<head>
		<meta charset="UTF-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
  		<title>Military Database</title>
  		<link rel="stylesheet" href="css/style.css">
  		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
	</head>

	<body>

		<header>
			<div class="header-left">
	      		<nav>
	      			<ul>
	      				<li><a href="login.php">Login</a></li>
			        </ul>
			    </nav>
		   	</div>
		</header>

		<main>

			<div class="order-lookup-container">
				<div class="order-lookup-container">
					<h1>Login</h1>
					<form action="login.php" method="POST">
						<div class="form-group">
							<label for="email">Employee ID:</label>
							<input type="email" id="email" name="email" required>
						</div>

						<div class="form-group">
							<label for="password">Password:</label>
							<input type="text" id="password" name="password" required>
						</div>

						<button type="submit">Submit Info</button>
					</form>
				</div>
				
				<?php if (!empty($info)): ?>
					<div class="order-details">

						<!-- 
				  		  -- TO DO: Fill in ALL the placeholders for this order from the db
  						  -->
						<h1>User Details</h1>
						<p><strong>Name: </strong> <?= $info['first_name'] ?></p>
				      
					</div>
				<?php endif; ?>

			</div>

		</main>

	</body>

</html>
