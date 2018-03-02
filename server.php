<?php 
	session_start();

	$email = '';
	$name = '';
	$password = '';
	$confirmPassword = '';
	$errors = array();
	$image_path = '';
	$location = '';
	$comment = '';
	$type_id = '';
	$issue_id = '';
	$image = '';
	$comment = '';

	//connect to the db
	$db = mysqli_connect('localhost', 'root', '', '4x4_db');	

	//if not get connection
	if (mysqli_connect_errno()) {
    	printf("Connection lost: %s\n", mysqli_connect_error());
    	session_destroy();
    	exit();
	}

	if (isset($_POST['register'])) {

		//to prevent sql injection
		$email = stripcslashes($email);
		$password = stripcslashes($password);
		$confirmPassword = stripcslashes($confirmPassword);
		$name = stripcslashes($name);

		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password = mysqli_real_escape_string($db, $_POST['password']);
		$confirmPassword = mysqli_real_escape_string($db, $_POST['confirmPassword']);
		$name = mysqli_real_escape_string($db, $_POST['name']);

		if (empty($email)) {
			array_push($errors, 'Email is required');
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  			array_push($errors, 'Invalid email format'); 
		}

		if (empty($password)) {
			array_push($errors, 'Password is required');
		} elseif (strlen($password) < 8) {
			array_push($errors, 'Password length must be 8 or more');
		} elseif (strlen($password) > 100) {
			array_push($errors, 'Password length must be less than 100');
		}

		if ($confirmPassword != $password) {
			array_push($errors, 'Passwords do not match');
		}

		if (!empty($name) && (strlen($name) < 4)) {
			array_push($errors, 'Name length must be more 4 or more');
		}

		if (count($errors) == 0) {
			$password = md5($password); //encrypt password (security)
			$sql = "INSERT INTO users (email, password, name) 
						VALUES ('$email', '$password', '$name')";
			mysqli_query($db, $sql) or die("Failet to connect to database".mysqli_error());
		
			$_SESSION['email'] = $email;
			$_SESSION['name'] = $name;
			$_SESSION['success'] = 'You are now logged in';
			header('location: index.php');
		}
	}


	//log user in from login page
	if(isset($_POST['login'])) {

		//to prevent sql injection
		$email = stripcslashes($email);
		$password = stripcslashes($password);

		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		//todo: if email already exist
		if (empty($email)) {
			array_push($errors, 'Email is required');
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  			array_push($errors, 'Invalid email format'); 
		}

		if (empty($password)) {
			array_push($errors, 'Password is required');
		}
		
		if(count($errors) == 0) {
			$password = md5($password); // encrypt password before comparing with one in db
			$query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
			$result = mysqli_query($db, $query) or die("Failet to connect to database".mysqli_error());
			if (mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_assoc($result);
				$_SESSION['email'] = $email;
				$_SESSION['name'] = $row['name'];
				$_SESSION['success'] = 'You are now logged in';
				header('location: index.php');
			} else {
				array_push($errors, "Wrong combination email / password");
			}

		}
	}



	//logout
	if(isset($_GET['logout'])){
		session_destroy();
		unset($_SESSION['email']);
		unset($_SESSION['name']);
		header('location: login.php');
	}

	if(isset($_POST['location'])) {
		 $location = $_POST['location'];
		 echo $location;
		// $_SESSION['location'] = $_POST['location'];
	}

	if(isset($_POST['type'])) {
		$type_id = $_POST['type'];
	}

	if(isset($_POST['issue'])) {
		$issue_id = $_POST['issue'];
	}

	if (isset($_POST['submit_ticket'])){

		$email = $_SESSION['email'];
		$query = "SELECT * FROM users WHERE email = '$email'";
		$result = mysqli_query($db, $query) or die("Failet to connect to database".mysqli_error());
		$row = mysqli_fetch_assoc($result);
		$user_id = $row['id'];

		$comment = stripcslashes($comment);
		$comment = mysqli_real_escape_string($db, $_POST['comment']);
		// $location = $_SESSION['location']

		$image_path = mysqli_real_escape_string($db, 'images/'.$_FILES['photo']['name']);
		if (preg_match("!image!", $_FILES['photo']['type'])) {
			if (copy($_FILES['photo']['tmp_name'], $image_path)) {
				$image = $image_path;
			}
		}
		//todo: if not empty (validation here)
		$query = "INSERT INTO tickets (user_id, type_id, issue_id, location, image, comment) 
						VALUES ('$user_id', '$type_id', '$issue_id', '$location', '$image', '$comment')";
		// mysqli_query($db, $query) or die("Failet to connect to database".mysqli_error());
						
		echo $query;

		

		// header('location: success.php');
	}

	//get types
	// if(isset($_GET['types']))
	// 	$query = "SELECT * FROM types";
	// 	$result = mysqli_query($db, $query) or die("Failet to connect to database".mysqli_error());
	// 	while (($row = mysql_fetch_row($result)) != null)
	// 	{
	// 		array_push($types_id, $row['id']);
	// 		array_push($types_name, $row['name']);
	// 	}

	//
 ?>