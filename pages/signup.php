<?php

	require '../vendor/autoload.php';
	require '../util/keys.php';
	require_once('../util/mysqli_connect.php');
	use \Mailjet\Resources;

	// Use your saved credentials, specify that you are using Send API v3.1

	$mj = new \Mailjet\Client($MJ_PUBLIC, $MJ_PRIVATE,true,['version' => 'v3.1']);

	$page_title = 'Create Account';
	include('../includes/header.html');

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		

		$errors = [];

		if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['pass1']) || empty($_POST['pass2'])) {
			$errors[] = 'Please ensure all fields are completely filled.';
		}

		if (!empty($_POST['first_name'])) {
			$fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
		}
		if (!empty($_POST['last_name'])) {
			$ln = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
		}

		if (!empty($_POST['email'])) {
			$e = mysqli_real_escape_string($dbc, trim($_POST['email']));

			$epattern = '/\b[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}\b/';
			if (preg_match($epattern, $e)) {
				$q = "SELECT user_id FROM users WHERE email ='$e'";
				$r = @mysqli_query($dbc, $q);
				if ($r) {
					$rc = mysqli_num_rows($r);
					if ($rc > 0) {
						$errors[] = 'That email address is already in use.';
					}
				}
			} else {
				$errors[] = 'Email address is not correctly formatted.';
			}
		}

		if (!empty($_POST['phone'])){
			$pn = mysqli_real_escape_string($dbc, trim($_POST['phone']));

			$pnpattern = '/\d{3}-\d{3}-\d{4}/';
			if (preg_match($pnpattern, $pn)) {
				$q = "SELECT user_id FROM users WHERE phone = '$pn'";
				$r = @mysqli_query($dbc, $q);
				$rc = mysqli_num_rows($r);
				if ($rc > 0) {
					$errors[] = 'That phone number is already in use.';
				}
			} else {
				$errors[] = 'Phone number is not correctly formatted. (Ex. 555-555-5555)';
			}
		}

		if (!empty($_POST['pass1'])) {
			if ($_POST['pass1'] != $_POST['pass2']) {
				$errors[] = 'Passwords do not match.';
			} else {
				$p = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
			}
		}

		if (empty($errors)) {

			$a = md5(uniqid(rand(), true));

			$q = "INSERT INTO users (first_name, last_name, email, phone, pass, active, registration_date) VALUES ('$fn', '$ln', '$e', '$pn', SHA2('$p', 512), '$a', NOW() )";
			$r = @mysqli_query($dbc, $q);
			if($r) {

				$url = 'http://localhost/eden/pages/activate.php?email='. urlencode($e) .'&id='. $a;

			$body = [
				'Messages' => [
					[
						'From' => [
							'Email' => "blazemckinlay@gmail.com",
							'Name' => "Eden's Seed Reserve"
						],
						'To' => [
							[
								'Email' => $e,
								'Name' => "$fn $ln"
							]
						],
						'Subject' => "Welcome to Eden's Seed Reserve!",
						'TextPart' => "Activation Email",
						'HTMLPart' => "<h3>Hello $fn,</h3><p>In order to login and access your account on Eden's Seed Reserve's website you need to activate your email.</p>
						<p>To activate your email, please follow this link:</p><br>
						<h4><a href=\"$url\">$url</a></h4><br>
						<p>We appreciate you taking the time to join our family!</p>"
					]
				]
			];

				$response = $mj->post(Resources::$Email, ['body' => $body]);
				$response->success();

				echo '<h1 style="text-align: center;">Thank you for registering, '. $fn .' '. stripslashes($ln) .'!</h1>
				<p style="text-align: center;">A confirmation email has been sent to your address. Please click on the link in that email in order to activate your account.</p><p style="text-align: center;">Make sure to check spam folders if you do not see your email.<br></p>';
				include('../includes/footer.html');
				exit();
			}

		}

	}
?>
</head>
<body>
	<div class="d-flex align-items-center flex-column">
		<?php
			if (isset($errors) && !empty($errors)) {
				echo '<p class="error" style="font-size: 15px;">The following error(s) occurred:<br>';
				foreach ($errors as $msg) {
					echo " - $msg<br>\n";
				}
				echo '<h5 style="text-align: center;">Please try again</h5></p>';
			}
		?>
		<div class="d-flex bg-success-subtle p-2 flex-column my-5">
			
		<h1>Signup</h1>
		<form action="signup.php" method="post" novalidate>
			<p>Please enter your information</p>
			<p>First Name: <input style="margin-left: 50px;" type="text" name="first_name" size="20" maxlength="40" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>"></p>
			<p>Last Name: <input style="margin-left: 52px;" type="text" name="last_name" size=20 maxlength="40" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name']; ?>"></p>
			<p>Email Address: <input style="margin-left: 27px;" type="email" name="email" size="20" maxlength="60" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>"></p>
			<p>Phone Number: <input style="margin-left: 20px;" type="text" name="phone" size="20" maxlength="40" value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>" placeholder="555-555-5555"></p>
			<p>Password: <input style="margin-left: 61px;" type="password" name="pass1" size="20" maxlength="20"></p>
			<p>Confirm Password: <input type="password" name="pass2" size="20" maxlength="20"></p>
			<p><input type="submit" name="submit" value="Register"></p>
		</form>
		</div>
	</div>
<?php
	include('../includes/footer.html');
?>