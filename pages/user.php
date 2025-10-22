<?php
	$page_title = 'User Page';
	include('../includes/header.html');
	require_once('../util/constants.php');

	if (!isset($_SESSION['user_id'])) {
		echo '<p class="error">This page has been accessed in error. Please login.</p>';
		include('../includes/footer.html');
		exit();
	}

	$id = $_SESSION['user_id'];

	$list = isset($_GET['list']) ? $_GET['list'] : 's';

	switch($list) {
		case 's':
			require_once('../util/mysqli_connect.php');

			$q = "SELECT * FROM users WHERE user_id = $id";
			$r = @mysqli_query($dbc, $q);
			$user = mysqli_fetch_array($r, MYSQLI_ASSOC);
			$feedback = [];

			if (isset($_POST['submit'])) {
				if ($_POST['newsletter'] != $user['newsletter']) {
					$n = $_POST['newsletter'];
					$q = "UPDATE users SET newsletter = $n WHERE user_id = $id";
					$r = @mysqli_query($dbc,$q);
					$feedback[] = '<p>Newsletter setting has been updated.</p>';
				}
				if (!empty($_POST['oldpass'])) {
					if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
						$p1 = mysqli_real_escape_string($dbc, $_POST['pass1']);
						$p2 = mysqli_real_escape_string($dbc, $_POST['pass2']);
						$op = mysqli_real_escape_string($dbc, $_POST['oldpass']);

						if ($p1 == $p2) {
							$q = "UPDATE users SET pass = SHA2('$p1', 512) WHERE user_id = $id AND pass = SHA2('$op', 512)";
							$r = @mysqli_query($dbc, $q);
							if (mysqli_affected_rows($dbc) != 0) {
								$feedback[] = '<p>Password has been updated.</p>';
							}
						} else {
							$feedback[] = '<p class="error">The two New Password fields must match.</p>';
						}
					} else {
						$feedback[] = '<p class="error">Neither of the two New Password fields can be empty</p>';
					}
					
				} elseif (!empty($_POST['pass1']) || !empty($_POST['pass2'])) {
					$feedback[] = '<p class="error">You must enter your current password to change passwords.</p>';
				}
				if (isset($_POST['email']) && $_POST['email'] != $user['email']) {
					$epattern = '/\b[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}\b/';
					$e = mysqli_real_escape_string($dbc, $_POST['email']);

					if (preg_match($epattern, $e)) {
						$q = "UPDATE users SET email = '$e' WHERE user_id = $id";
						$r = @mysqli_query($dbc, $q);
						if (mysqli_affected_rows($dbc) != 0) {
							$feedback[] = '<p>Email has been updated.</p>';
						}
						
					} else {
						$feedback[] = '<p class="error">The new email is not a real email.</p>';
					}
				}
			}

			$q = "SELECT * FROM users WHERE user_id=$id";
			$r = @mysqli_query($dbc, $q);
			$user = mysqli_fetch_array($r, MYSQLI_ASSOC);

			$content = '
				<form method="post">
					<h5>Receive Newsletter:</h5>
					Yes <input type="radio" name="newsletter" value=1 '. ($user['newsletter'] ? 'checked' : '') .'> &nbsp&nbsp&nbsp&nbsp No <input type="radio" name="newsletter" value=0 '. ($user['newsletter'] ? '' : 'checked') .'><br><br>
					<h5>Change Password:</h5>
					<p>Current Password:<br>
					<input type="password" name="oldpass">
					</p>
					<p>New Password:<br>
					<input type="password" name="pass1">
					</p>
					<p>Confirm New Password:<br>
					<input type="password" name="pass2">
					</p>
					<h5>Change Email:</h5>
					<p>Email:<br>
					<input type="email" name="email" value='. $user['email'] .'>
					</p>
					<input type="submit" class="btn btn-secondary" value="Submit Changes" name="submit">
				</form>
			';

			if ($feedback) {
				foreach($feedback as $i) {
					$content .= $i;
				}
			}
			break;
		case 'i':
			
			if (isset($_POST['submit'])) {
				$feedback = [];

				if (empty($_POST['line_1'])) {
					$feedback[] = '<p class="error">Missing Street Address.</p>';
				} elseif (!preg_match('/^\d+ [a-zA-Z]+ \d* ?[a-zA-Z]+/', $_POST['line_1'])) {
					$feedback[] = '<p class="error">Please ensure the Street Address is properly formatted.</p>';
				} else {
					$l1 = mysqli_real_escape_string($dbc, $_POST['line_1']);
					$l2 = null;
					if (!empty($_POST['line_2']) && preg_match('/^[a-zA-Z#\d]{0,10}/', $_POST['line_2'])) {
						$l2 = mysqli_real_escape_string($dbc, $_POST['line_2']);
					} elseif(!empty($_POST['line_2'])) {
						$feedback[] = '<p class="error">Please ensure the Unit Number in Address Line 2 is properly formatted.</p>';
					}
				}

				if (empty($_POST['city'])) {
					$feedback[] = '<p class="error">Missing City.</p>';
				} elseif (!preg_match('/^[a-zA-Z ]{1,50}/', $_POST['city'])) {
					$feedback[] = '<p class="error">Please ensure the City is properly formatted.</p>';
				} else {
					$c = mysqli_real_escape_string($dbc, $_POST['city']);
				}

				if (empty($_POST['zip'])) {
					$feedback[] = '<p class="error">Missing Zip Code.</p>';
				} elseif (!preg_match('/^\d{5}(-\d{4})?/', $_POST['zip'])) {
					$feedback[] = '<p class="error">Please ensure the Zip Code is properly formatted.</p>';
				} else {
					$z = mysqli_real_escape_string($dbc, $_POST['zip']);
				}

				if (empty($_POST['state'])) {
					$feedback[] = '<p class="error">Missing State.</p>';
				} elseif (!in_array($_POST['state'], $states)) {
					$feedback[] = '<p class="error">Please ensure the State is properly formatted (XX Abbreviation).';
				} else {
					$s = mysqli_real_escape_string($dbc, $_POST['city']);
				}

				if (!$feedback) {
					$q = "SELECT * FROM addresses WHERE user_id=$id";
					$r = @mysqli_query($dbc, $q);
					$rc = mysqli_num_rows($r);

					if ($rc == 0) {
						$q = "INSERT INTO addresses(user_id, line_1, line_2, city, zip, state) VALUES ($id, '$l1', '$l2', '$c', '$z', '$s')";
						$r = @mysqli_query($dbc, $q);
						if (mysqli_affected_rows($dbc) > 0) {
							$feedback[] = '<p>Address successfully added.</p>';
						} else {
							$feedback[] = '<p class="error">Address could not be added, please contact system administrator.</p>';
						}
					} elseif ($rc == 1) {
						$q = "UPDATE addresses SET line_1 = '$l1', line_2 = '$l2', city = '$c', zip = '$z', state = '$s' WHERE user_id = $id";
						$r = @mysqli_query($dbc, $q);
						if (mysqli_affected_rows($dbc) > 0) {
							$feedback[] = '<p>Address successfully updated.</p>';
						} else {
							$feedback[] = '<p class="error">Address could not be updated, please contact system administrator.</p>';
						}
					}
				}

			}

			$content = '
			<form method="post">
			<h5>Shipping and Billing Address:</h5>
			<p>Street Address Line 1:<br>
			<input type="text" name="line_1"><br>
			Street Address Line 2:<br>
			<input type="text"><br>
			City<br>
			<input type="text" name="city">
			Zipcode &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp State<br>
			<input type="text" size=10 name="zip"> <input list="states" style="width: 70px;" name="state">
				<datalist id="states">
			';
				foreach($states as $state) {
					$content .= '<option value='. $state .'>';
				}
			$content .= '
				</datalist><br>
				</p>
			<input type="submit" class="btn btn-secondary" name="submit" value="Submit Changes">
			</form>
			';

			if ($feedback) {
				foreach($feedback as $i) {
					$content .= $i;
				}
			}
			break;
		case 'd':
			$content = '';
			break;
		default:
			break;
	}

?>
</head>
<body>
	<h1><?php echo $_SESSION['first_name']; ?></h1>
	<div class="d-flex flex-direction-row">
		<div class="list-group">
			<a href="../pages/user.php?list=s" class="list-group-item list-group-item-action"><h4>Settings</h4></a>
			<a href="../pages/user.php?list=i" class="list-group-item list-group-item-action"><h4>Personal Info</h4></a>
			<a href="../pages/orders.php" class="list-group-item list-group-item-action"><h4>Past Orders</h4></a>
			<a href="../pages/user.php?list=d" class="list-group-item list-group-item-action"><h4>Delete Account</h4></a>
		</div>
		<div class="content border" style="margin-left: 100px; padding-top: 20px; padding-bottom: 50px; padding-left: 40px; padding-right: 120px; max-width: 400px;">
		<?php echo $content ?>
		</div>
	</div>
<?php
	include('../includes/footer.html');
?>