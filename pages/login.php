<?php
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		require('../includes/login_functions.inc.php');
		require_once('../util/mysqli_connect.php');

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
		// Include the header:
    	$page_title = 'Login';
	}
	include_once('../includes/header.html');
    echo '</head>
          <body>';

    // Print any error messages, if they exist:

    // Display the form:
    ?>
    <div class="d-flex align-items-center p-5 flex-column">
		<?php
			if (isset($errors) && !empty($errors)) {
				echo '<p class="error" style="font-size: 15px;">The following error(s) occurred:<br>';
				foreach ($errors as $msg) {
					echo " - $msg<br>\n";
				}
				echo '<h5>Please try again</h5></p>';
			}
		?>
    <div class="d-flex justify-content-center flex-column bg-success-subtle align-items-center p-5" style="max-width: 50%;">
        <h1>Login</h1><br>
        <form action="login.php" method="post" novalidate>
            <p>Email Address: <input type="email" name="email" size="20" maxlength="60" value=<?php echo isset($_POST['email']) ? '"'. $_POST['email'] .'"' : ""; ?>></p>
            <p>Password: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="password" name="pass" size="20" maxlength="20" style="margin-left: 2px;"></p><br><br>
            <input type="submit" name="submit" class="btn btn-info w-100 my-3 fancy" value="Login">
			
        </form>
    </div>
		
    </div>

<?php include('../includes/footer.html'); ?>