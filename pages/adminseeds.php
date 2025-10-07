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

	$q = "SELECT seed_id, seed_name FROM seeds ORDER BY $order_by LIMIT $start, $display";
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
				<td><a href="editseed.php?id='. $row['seed_id'] .'">Edit</a></td>
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropDelete'. $row['seed_id'] .'">Delete</button></td>
				<td>'. $row['seed_id'] .'</td>
				<td>'. $row['seed_name'] .'</td>
			</tr>';

		echo '

			<!-- Modal -->
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
					<form action="../util/deleteseed.php?id='. $row['seed_id'] .'" method="post">
						<input type="submit" class="btn btn-primary" value="DELETE">
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
			echo '<a href="adminseeds.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'&order='. $order .'">Previous</a> ';
		}

		for ($i = 1; $i <= pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="adminseeds.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'">'. $i .'</a> ';
			} else {
				echo $i .' ';
			}
		}

		if ($current_page != $pages) {
			echo '<a href="adminseeds.php?s='. ($start + display) .'&p='. $pages .'$sort='. $sort .'">Next</a>';
		}
		
	}
	echo '</p>';
	echo '<form action="addseed.php" novalidate>
		  <input type="submit" name="submit" value="Add New Seed">
		  </form>';
?>

<script type="module" src="../util/adminmodals.js"></script>
			
</div>

<?php
	include('../includes/footer.html');
?>