<?php
	$page_title = 'Admin Seeds';
	include('../includes/header.html');
?>
</head>
<body>
<?php
	echo '<h1>Seeds</h1>';

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
			$order_by = 'seed_id ASC';
			break;
		case 'sna':
			$order_by = 'seed_name ASC';
			break;
		case 'idd':
			$order_by = 'seed_id DESC';
			break;
		case 'snd':
			$order_by = 'seed_name DESC';
			break;
		default:
			$order_by = 'seed_id ASC';
			$sort = 'ida';
			break;
	}

	$q = "SELECT * FROM seeds ORDER BY $order_by LIMIT $start, $display";
	$r = @mysqli_query($dbc, $q);

	echo '
		<table width="60%" class="left">
			<thead>
				<tr>
					<th>Edit</th>
					<th>Delete</th>
					<th><a href="adminseeds.php?sort='. ($order_by == "seed_id ASC" ? "idd" : "ida") .'">Seed ID</a></th>
					<th><a href="adminseeds.php?sort='. ($order_by == "seed_name ASC" ? "snd" : "sna") .'">Seed Name</a></th>
				</tr>
			</thead>
			<tbody>';

	$bg = '#eeeeee';

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		
		echo '
			<tr bgcolor="'. $bg .'">
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit'. $row['seed_id'] .'">Edit</button></td>
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropDelete'. $row['seed_id'] .'">Delete</button></td>
				<td>'. $row['seed_id'] .'</td>
				<td>'. $row['seed_name'] .'</td>
			</tr>';

			echo '

			<!-- Edit Modal -->
			<div class="modal fade" id="staticBackdropEdit'. $row['seed_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<form method="post">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Seed - '. $row['seed_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Name: <input type="text" name="name" value="'. (isset($row['seed_name']) ? $row['seed_name'] : null) .'"></p>
					<p>Blurb: <input type="text" name="blurb" value="'. (isset($row['seed_blurb']) ? $row['seed_blurb'] : null) .'"></p>
					<p>Description:</p> <textarea name="desc" rows="5" cols="40">'. (isset($row['seed_desc']) ? $row['seed_desc'] : null) .'</textarea>
					<p>Image: To be added</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="edit" value='. $row['seed_id'] .'>
				</div>
				</div>
			</div>
			</form>
			</div>
		';


		echo '

			<!-- Delete Modal -->
			<div class="modal fade" id="staticBackdropDelete'. $row['seed_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Seed - '. $row['seed_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete entry for <strong>'. $row['seed_name'] .'</strong>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<form method="post">
						<input type="submit" name="submit" value="DELETE" class="btn btn-primary">
						<input type="hidden" name="delete" value='. $row['seed_id'] .'>
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
			echo '<a href="adminseeds.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'">Previous</a> ';
		}

		for ($i = 1; $i <= $pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="adminseeds.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'">'. $i .'</a> ';
			} else {
				echo $i .' ';
			}
		}

		if ($current_page != $pages) {
			echo '<a href="adminseeds.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'">Next</a>';
		}
		
	}
	echo '</p>';
	echo '
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropAdd">Add New Seed</button>

			<!-- Add Modal -->
			<div class="modal fade" id="staticBackdropAdd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<form method="post">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Add Seed</h1>
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

<script type="module">
	function deleteEntry() {
		require('../util/adminmodals.js');
	}
</script>
			
</div>

<?php
	include('../includes/footer.html');
?>