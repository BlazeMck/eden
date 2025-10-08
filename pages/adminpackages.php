<?php
	$page_title = 'Admin Packages';
	include('../includes/header.html');
?>
</head>
<body>
<?php
	echo '<h1>Packages</h1>';

	if ($_SESSION['user_level'] != 0) {
		echo '<p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	require_once('../util/mysqli_connect.php');

	$q = "SELECT seed_id, seed_name FROM seeds";
	$r = @mysqli_query($dbc, $q);
	$phpData = [];
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$newData = ['id' => $row['seed_id'], 'name' => $row['seed_name']];
		array_push($phpData, $newData);
	}
	$jsonData = json_encode($phpData);

	$display = 5;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	 	if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
		
			$id = $_POST['delete'];

			$q = "SELECT p.package_id, p.package_name FROM packages AS p LEFT JOIN package_contents AS pc ON p.package_id=pc.package_id WHERE pc.seed_id=$id";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) > 0) {
				echo 'Seed found in packages, please remove seed from the following packages before attempting to delete:';
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					echo '<p>Package '. $row['package_id'] .' - '. $row['package_name'] .'</p>';
				}
			} else {
				$q = "DELETE FROM seeds WHERE seed_id=$id LIMIT 1";
				$r = @mysqli_query($dbc, $q);
				if (mysqli_affected_rows($dbc) == 1) {
					
					echo '<p>The seed has been deleted.</p>';

				} else {
					echo '<p class ="error">The seed could not be deleted due to a system error.</p>';
					echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
				}
			}
		} elseif (isset($_POST['edit']) && is_numeric($_POST['edit'])) {

			$id = $_POST['edit'];
			$name = $_POST['name'];
			$blurb = $_POST['blurb'];
			$desc = $_POST['desc'];

			$q = "UPDATE seeds SET seed_name = '$name', seed_blurb = '$blurb', seed_desc = '$desc', image_uri = 'changedtbd' WHERE seed_id = $id";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) {
				echo "<p>The seed - $name - has been updated.</p>";
			} else {
				echo '<p class="error">The seed could not be updated due to a system error.</p>';
				echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
			}
		} elseif (isset($_POST['add']) && is_numeric($_POST['add'])) {
			$name = $_POST['name'];
			$blurb = $_POST['blurb'];
			$desc = $_POST['desc'];

			$q = "INSERT INTO seeds(seed_name, seed_blurb, seed_desc, image_uri) VALUES('$name', '$blurb', '$desc', 'addedtbd')";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) {
				echo "<p>The seed - $name - has been added.</p>";
			} else {
				echo '<p class="error">The seed could not be added due to a system error.</p>';
				echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
			}
		}
	}

	if (isset($_GET['p']) && is_numeric($_GET['p'])) {

		$pages = $_GET['p'];

	} else {

		$q = "SELECT COUNT(seed_id) FROM seeds";
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
			$order_by = "package_id ASC";
			break;
		case 'idd':
			$order_by = "package_id DESC";
			break;
		case 'pna':
			$order_by = "package_name ASC";
			break;
		case 'pnd':
			$order_by = "package_name DESC";
			break;
		case 'ppa':
			$order_by = "package_price ASC";
			break;
		case 'ppd':
			$order_by = "package_price DESC";
			break;
		default:
			$order_by = "package_id ASC";
			$sort = 'ida';
			break;

	}

	$q = "SELECT * FROM packages ORDER BY $order_by LIMIT $start, $display";
	$r = @mysqli_query($dbc, $q);

	echo '
		<table width="60%" class="left">
			<thead>
				<tr>
					<th>Edit</th>
					<th>Delete</th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_id ASC" ? "idd" : "ida") .'">Package ID</a></th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_name ASC" ? "pnd" : "pna") .'">Package Name</a></th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_price ASC" ? "ppd" : "ppa") .'">Package Price</a></th>
					<th>Contents</th>
				</tr>
			</thead>
			<tbody>';

	$bg = '#eeeeee';

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		
		echo '
			<tr bgcolor="'. $bg .'">
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit'. $row['package_id'] .'">Edit</button></td>
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropDelete'. $row['package_id'] .'">Delete</button></td>
				<td>'. $row['package_id'] .'</td>
				<td>'. $row['package_name'] .'</td>
				<td>'. $row['package_price'] .'</td>
				<td>';
			$id = $row['package_id'];
			$q = "SELECT s.seed_name, s.seed_id, p.package_id FROM packages AS p JOIN package_contents AS pc ON p.package_id = pc.package_id JOIN seeds AS s ON s.seed_id=pc.seed_id WHERE p.package_id = $id";
			$result = @mysqli_query($dbc, $q);
			while ($cRow = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				echo '<p>'. $cRow['seed_id'] .' - '. $cRow['seed_name'] .'</p>';
			}
			echo '</td></tr>';

			echo '

			<!-- Edit Modal -->
			<div class="modal fade" id="staticBackdropEdit'. $row['package_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<form method="post">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Package - '. $row['package_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Name: <input type="text" name="name" value="'. (isset($row['package_name']) ? $row['package_name'] : null) .'"></p>
					<p>Price: <input type="number" name="price" value="'. (isset($row['package_price']) ? $row['package_price'] : null) .'"></p>
					<p>Description:</p> <textarea name="desc" rows="5" cols="40">'. (isset($row['package_desc']) ? $row['package_desc'] : null) .'</textarea>
					<div id="content_select">
						<p>Contents: 
					</div>
					<p>Image: To be added</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="edit" value='. $row['package_id'] .'>
				</div>
				</div>
			</div>
			</form>
			</div>
		';


		echo '

			<!-- Delete Modal -->
			<div class="modal fade" id="staticBackdropDelete'. $row['package_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Package - '. $row['package_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete entry for <strong>'. $row['package_name'] .'</strong>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<form method="post">
						<input type="submit" name="submit" value="DELETE" class="btn btn-primary">
						<input type="hidden" name="delete" value='. $row['package_id'] .'>
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
			echo '<a href="adminpackages.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'">Previous</a> ';
		}

		for ($i = 1; $i <= $pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="adminpackages.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'">'. $i .'</a> ';
			} else {
				echo $i .' ';
			}
		}

		if ($current_page != $pages) {
			echo '<a href="adminpackages.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'">Next</a>';
		}
		
	}
	echo '</p>';
	echo '
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropAdd">Add New Package</button>

			<!-- Add Modal -->
			<div class="modal fade" id="staticBackdropAdd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<form method="post">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Add Package</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Name: <input type="text" name="name" value=""></p>
					<p>Blurb: <input type="text" name="blurb" value=""></p>
					<p>Description:</p> <textarea name="desc" rows="5" cols="40"></textarea>
					<p>Image: To be added</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="add" value=0>
				</div>
				</div>
			</div>
			</form>
			</div>
		';
?>
			
</div>

<script src="../util/adminmodals.js"></script>

<?php
	include('../includes/footer.html');
?>