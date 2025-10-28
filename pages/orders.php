<?php
	if (isset($_POST['search'])) {
		header('Location: ../pages/order.php?id='. $_POST['search']);
	}
	$page_title = 'Orders';
	include('../includes/header.html');

	require_once('../util/mysqli_connect.php');
?>
</head>
<body>
	<?php 
		$display = 10;
		if (isset($_SESSION['user_id'])) {

			$id = $_SESSION['user_id'];

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
				case 'ida':
					$order_by = 'order_id ASC';
					break;
				case 'oda':
					$order_by = 'order_date ASC';
					break;
				case 'sta':
					$order_by = 'subtotal ASC';
					break;
				case 'idd':
					$order_by = 'order_id DESC';
					break;
				case 'odd':
					$order_by = 'order_date DESC';
					break;
				case 'std':
					$order_by = 'subtotal DESC';
					break;
				default:
					$order_by = 'order_id ASC';
					$sort = 'ida';
					break;
			}

			$q = "SELECT order_id, order_date, subtotal FROM orders WHERE customer_id = $id ORDER BY $order_by LIMIT $start, $display";
			$r = @mysqli_query($dbc, $q);	
			$rc = mysqli_num_rows($r);

			echo '<h1>Orders - '. $_SESSION['first_name'] .'</h1>
				<div class="d-flex align-items-center flex-column">';
			if ($rc != 0){
				echo '
					<table width="80%" class="left">
						<thead>
							<tr>
								<th><a href="orders.php?sort='. ($order_by == "order_id ASC" ? "idd" : "ida") .'">Order Number</a></th>
								<th><a href="orders.php?sort='. ($order_by == "order_date ASC" ? "odd" : "oda") .'">Order Date</a></th>
								<th><a href="orders.php?sort='. ($order_by == "subtotal ASC" ? "std" : "sta") .'">Order Subtotal</a></th>
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
							<td>'. $row['subtotal'] .'</td>
							<td><a href="../pages/order.php?id='. $row['order_id'] .'"><button type="button" class="btn btn-light">View Order</button></a></td>
						</tr>';
				}

				mysqli_free_result($r);
				mysqli_close($dbc);

				echo '<br><p>';
				if ($pages > 1) {

					$current_page = ($start/$display) + 1;

					if ($current_page != 1) {
						echo '<a href="orders.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'">Previous</a> ';
					}

					for ($i = 1; $i <= $pages; $i++) {
						if ($i != $current_page) {
							echo '<a href="orders.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'">'. $i .'</a> ';
						} else {
							echo $i .' ';
						}
					}

					if ($current_page != $pages) {
						echo '<a href="orders.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'">Next</a>';
					}
					
					echo '</div>';
				}
			} else {
				echo '<h3>You have not placed any orders.</h3>';
			}
		} else {
			echo '<h1>Orders - Order Lookup</h1>
				  	<form method="post" action>
						<div class="input-group d-flex justify-content-center">
							<span class="input-group-text" id="basic-addon1">Order # -</span>
							<input class="" type="search" placeholder="Search" aria-label="Search" name="search" size="90"/>
			  				<button class="btn btn-outline-success" style="background-color: aliceblue;" type="submit">Search</button>
						</div>
					</form>';
		}
		
		include('../includes/footer.html');
	?>