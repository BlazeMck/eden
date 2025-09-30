<?php
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		require('../includes/login_functions.inc.php');
		require('../util/mysqli_connect.php');

		// Passes information gathered from form to login_function for validating login attempt
		list($check, $data) = check_login($dbc, $_POST['email'], $_POST['pass']);

		if ($check) {

			session_start();
			$_SESSION['user_id'] = $data['user_id'];
			$_SESSION['first_name'] = $data['first_name'];
			$_SESSION['last_name'] = $data['last_name'];
			$_SESSION['email'] = $data['email'];
			$_SESSION['user_level'] = $data['user_level'];
			$_SESSION['cart'] = [];

			$_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);

			redirect_user();
		} else {
			$errors = $data;
		}

		mysqli_close($dbc);
	}

	include('../includes/login_page.inc.php');
?>