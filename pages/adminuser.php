<?php
	$page_title = 'Admin Users';
	include('../includes/header.html');
?>
</head>
<body>
<?php
	echo '<h1>Users</h1>';

	if ($_SESSION['user_level'] != 0) {
		echo '<p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	require_once('../util/mysqli_connect.php');
	$display = 10;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	 	if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
		
			$id = $_POST['delete'];

			$q = "UPDATE orders customer_id = NULL WHERE customer_id = $id";
			$r = @mysqli_query($dbc, $q);

			$q = "DELETE FROM users WHERE user_id=$id LIMIT 1";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) {
				
				echo '<p>The user has been deleted.</p>';

			} else {
				echo '<p class ="error">The user could not be deleted due to a system error.</p>';
				echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
			}
			
		} elseif (isset($_POST['edit']) && is_numeric($_POST['edit'])) {

			$id = $_POST['edit'];
			$fn = $_POST['fn'];
			$ln = $_POST['ln'];
			$e = $_POST['e'];
			$pn = $_POST['pn'];
			$ul = $_POST['ul'];

			$q = "UPDATE users SET first_name = '$fn', last_name = '$ln', email = '$e', phone = '$pn', user_level = $ul WHERE user_id = $id";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) {
				echo "<p>The user - $fn $ln - has been updated.</p>";
			} else {
				echo '<p class="error">The user could not be updated due to a system error.</p>';
				echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
			}
		}
	}

	if (isset($_GET['p']) && is_numeric($_GET['p'])) {

		$pages = $_GET['p'];

	} else {

		$q = "SELECT COUNT(user_id) FROM users";
		$r = @mysqli_query($dbc, $q);
		$row = @mysqli_fetch_array($r, MYSQLI_NUM);
		$records = $row[0];

		if ($records > $display) {
			$pages = ceil ($records/$display);
		} else {
			$pages = 1;
		}
	}

	if (isset($_GET['s']) && is_numeric($_GET['s'])) {
		$start = $_GET['s'];
	} else {
		$start = 0;
	}

	$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'id';

	switch ($sort) {
		case 'ida':
			$order_by = 'user_id ASC';
			break;
		case 'fna':
			$order_by = 'first_name ASC';
			break;
		case 'lna':
			$order_by = 'last_name ASC';
			break;
		case 'idd':
			$order_by = 'user_id DESC';
			break;
		case 'fnd':
			$order_by = 'first_name DESC';
			break;
		case 'lnd':
			$order_by = 'last_name DESC';
		default:
			$order_by = 'user_id ASC';
			$sort = 'ida';
			break;
	}

	$q = "SELECT * FROM users ORDER BY $order_by LIMIT $start, $display";
	$r = @mysqli_query($dbc, $q);

	echo '
		<table width="75%" class="left">
			<thead>
				<tr>
					<th>Edit</th>
					<th>Delete</th>
					<th>Orders</th>
					<th><a href="adminuser.php?sort='. ($order_by == "user_id ASC" ? "idd" : "ida") .'">User ID</a></th>
					<th><a href="adminuser.php?sort='. ($order_by == "first_name ASC" ? "fnd" : "fna") .'">First Name</a></th>
					<th><a href="adminuser.php?sort='. ($order_by == "last_name ASC" ? "lnd" : "lna") .'">Last Name</a></th>
					<th>Email</th>
					<th>Phone Number</th>
					<th>User Level</th>
				</tr>
			</thead>
			<tbody>';

	$bg = '#eeeeee';

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		
		echo '
			<tr bgcolor="'. $bg .'">
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit'. $row['user_id'] .'">Edit</button></td>
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropDelete'. $row['user_id'] .'">Delete</button></td>
				<td><a href="orders.php?uid='. $row['user_id'] .'" class="btn btn-primary">Orders</a></td>
				<td>'. $row['user_id'] .'</td>
				<td>'. $row['first_name'] .'</td>
				<td>'. $row['last_name'] .'</td>
				<td>'. $row['email'] .'</td>
				<td>'. $row['phone'] .'</td>
				<td>'. $row['user_level'] .'</td>
			</tr>';

			echo '

			<!-- Edit Modal -->
			<div class="modal fade" id="staticBackdropEdit'. $row['user_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<form method="post">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit User - '. $row['first_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>First Name: <input type="text" name="fn" value="'. (isset($row['first_name']) ? $row['first_name'] : null) .'"></p>
					<p>Last Name: <input type="text" name="ln" value="'. (isset($row['last_name']) ? $row['last_name'] : null) .'"></p>
					<p>Email: <input type="email" name="e" value="'. (isset($row['email']) ? $row['email'] : null) .'"></p>
					<p>Phone Number: <input type="text" name="pn" value="'. (isset($row['phone']) ? $row['phone'] : null) .'" placeholder="555-555-5555"></p>
					<p>Admin?: <input type="radio" id="yesAdmin" name="ul" value=0><label for="yesAdmin">Yes</label> <input type="radio" id="noAdmin" name="ul" value=1 checked><label for="noAdmin">No</label></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="edit" value='. $row['user_id'] .'>
				</div>
				</div>
			</div>
			</form>
			</div>
		';


		echo '

			<!-- Delete Modal -->
			<div class="modal fade" id="staticBackdropDelete'. $row['user_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Delete User - '. $row['first_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete entry for <strong>'. $row['first_name'] .' '. $row['last_name'] .'</strong>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<form method="post">
						<input type="submit" name="submit" value="DELETE" class="btn btn-primary">
						<input type="hidden" name="delete" value='. $row['user_id'] .'>
					</form>
				</div>
				</div>
			</div>
			</div>
		';
	}

	echo '</tbody></table>';

	mysqli_free_result($r);
	mysqli_close($dbc);

	echo '<br><p>';
	if ($pages > 1) {
		

		$current_page = ($start/$display) + 1;

		if ($current_page != 1) {
			echo '<a href="adminuser.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'">Previous</a> ';
		}

		for ($i = 1; $i <= $pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="adminuser.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'">'. $i .'</a> ';
			} else {
				echo $i .' ';
			}
		}

		if ($current_page != $pages) {
			echo '<a href="adminuser.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'">Next</a>';
		}
		
	}
	echo '</p>';
?>
			
</div>

<?php
	include('../includes/footer.html');
?>