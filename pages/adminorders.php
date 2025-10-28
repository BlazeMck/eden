<?php
	if (isset($_POST['search'])) {
		header('Location: ../pages/order.php?id='. $_POST['search']);
	}
	$page_title = 'Orders';
	include('../includes/header.html');

	if ($_SESSION['user_level'] != 0) {
		echo '</head><body><p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	require_once('../util/mysqli_connect.php');
?>
</head>
<body>
	<h1>All Orders - Admin List</h1>
	<div class="d-flex align-items-center flex-column">
	<form method="post" action>
		<div class="input-group">
			<span class="input-group-text" id="basic-addon1">Order # -</span>
			<input class="" type="search" placeholder="Search" aria-label="Search" name="search" size="90"/>
			<button class="btn btn-outline-success" type="submit">Search</button>
		</div>
	</form>
	<?php 
		$display = 10;

		if (isset($_GET['p']) && is_numeric($_GET['p'])) {

			$pages = $_GET['p'];
	
		} else {
	
			$q = "SELECT COUNT(order_id) FROM orders";
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
			case 'oida':
				$order_by = 'o.order_id ASC';
				break;
			case 'oda':
				$order_by = 'o.order_date ASC';
				break;
			case 'sta':
				$order_by = 'o.subtotal ASC';
				break;
			case 'fna':
				$order_by = 'u.first_name ASC';
				break;
			case 'lna':
				$order_by = 'u.last_name ASC';
				break;
			case 'dca':
				$order_by = 'o.delivery_city ASC';
				break;
			case 'dsa':
				$order_by = 'o.delivery_state ASC';
				break;
			case 'oidd':
				$order_by = 'o.order_id DESC';
				break;
			case 'odd':
				$order_by = 'o.order_date DESC';
				break;
			case 'std':
				$order_by = 'o.subtotal DESC';
				break;
			case 'fnd':
				$order_by = 'u.first_name DESC';
				break;
			case 'lnd':
				$order_by = 'u.last_name DESC';
				break;
			case 'dcd':
				$order_by = 'o.delivery_city DESC';
				break;
			case 'dsd':
				$order_by = 'o.delivery_state DESC';
				break;
			default:
				$order_by = 'order_id ASC';
				$sort = 'ida';
				break;
		}

		$q = "SELECT o.order_id, o.order_date, o.subtotal, u.first_name, u.last_name, o.delivery_city, o.delivery_state FROM orders AS o LEFT JOIN users AS u ON o.customer_id = u.user_id ORDER BY $order_by LIMIT $start, $display";
		$r = @mysqli_query($dbc, $q);	
		$rc = mysqli_num_rows($r);

		if ($rc != 0){
			echo '
				<table width="80%" class="left">
					<thead>
						<tr>
							<th><a href="adminorders.php?sort='. ($order_by == "o.order_id ASC" ? "oidd" : "oida") .'">Order Number</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "o.order_date ASC" ? "odd" : "oda") .'">Order Date</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "u.first_name ASC" ? "fnd" : "fna") .'">First Name</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "u.last_name ASC" ? "lnd" : "lna") .'">Last Name</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "o.delivery_city ASC" ? "dcd" : "dca") .'">City</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "o.delivery_state ASC" ? "dsd" : "dsa") .'">State</a></th>
							<th><a href="adminorders.php?sort='. ($order_by == "o.subtotal ASC" ? "std" : "sta") .'">Order Subtotal</a></th>
							<th>View Order Details</th>
						</tr>
					</thead>
					<tbody>';

			$bg = '#eeeeee';
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
	
				echo '
					<tr bgcolor="'. $bg .'">
						<td>'. $row['order_id'] .'</td>
						<td>'. $row['order_date'] .'</td>
						<td>'. $row['first_name'] .'</td>
						<td>'. $row['last_name'] .'</td>
						<td>'. $row['delivery_city'] .'</td>
						<td>'. $row['delivery_state'] .'</td>
						<td>'. $row['subtotal'] .'</td>
						<td><a href="../pages/order.php?id='. $row['order_id'] .'"><button type="button" class="btn btn-light">View Order</button></a></td>
					</tr>';
			}

			mysqli_free_result($r);
			mysqli_close($dbc);

			if ($pages > 1) {

				$current_page = ($start/$display) + 1;

				echo '<div class="d-flex">';
				if ($current_page != 1) {
					echo '<a href="adminorders.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'" class="btn btn-secondary">Previous</a> ';
				}

				for ($i = ($current_page - 2 < 1 ? 1 : $current_page - 2); $i <= ($current_page + 2 > $pages ? $pages : $current_page + 2); $i++) {
					if ($i != $current_page) {
						echo '<a href="adminorders.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'" class="btn btn-secondary">'. $i .'</a> ';
					} else {
						echo '<span class="btn btn-primary">'.$i .'</span>';
					}
				}

				if ($current_page != $pages) {
					echo '<a href="adminorders.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'" class="btn btn-secondary">Next</a>';
				}
				echo '</div>';
			}
		}
	?>
	</div>
<?php
	include('../includes/footer.html');
?>