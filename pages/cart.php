<?php
	$page_title = 'Cart';
	include('../includes/header.html');
?>
</head>
<body>
	<h1>Cart</h1>
	<div class="d-flex w-100">
		<div class="p-2 me-auto">
	<?php 
		$cart = null;

		if (!empty($_SESSION['cart'])) {
			$cart = $_SESSION['cart'];
		}

		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if ((isset($_POST['qty']) && is_numeric($_POST['qty'])) && (isset($_POST['id']) && is_numeric($_POST['id']))) {
				if ($_POST['qty'] == 0) {
					function removeFromCart($var) {
						if ($var != $_POST['id']) {
							return $var;
						}
					}

					$cart = array_filter($cart, 'removeFromCart', ARRAY_FILTER_USE_KEY);
				} else {
					$cart[$_POST['id']] = $_POST['qty'];
				}
			} elseif (isset($_POST['delete'])) {
				function removeFromCart($var) {
					if ($var != $_POST['delete']) {
						return $var;
					}
				}

				$cart = array_filter($cart, 'removeFromCart', ARRAY_FILTER_USE_KEY);
				
			}

			if (isset($_SESSION['user_id'])) {
				$_SESSION['cart'] = $cart;
			} else {
				$_COOKIE['cart'] = json_encode($cart);
			}
		}

		

		if ($cart) {
			require_once('../util/mysqli_connect.php');

			$filter = '';
			$num = 1;
			foreach ($cart as $id => $item) {
				$filter .= "package_id = $id";
				if ($num != count($cart)) {
					$filter .= " OR ";
				}
				$num++;
			}

			echo '
			<div class="d-flex align-items-center justify-content-center">
			<div class="d-flex justify-content-center flex-column">
				<table class="table table-secondary table-hover align-middle table-lg">
					<thead>
						<tr>
							<th></th>
							<th>Package</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Total</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>';
			$q = "SELECT * FROM packages WHERE $filter";
			$r = @mysqli_query($dbc, $q);

			$allTotal = 0;
			$allQty = 0;
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$id = $row['package_id'];
				$qty = $cart[$id];
				$allQty += $qty;
				$total = $row['package_price'] * $qty;
				$allTotal += $total;

				$src = "../includes/media/cornucopia-temp-DONOTPUBLISH.jpg";

				if (file_exists($row['image_uri'])) {
					$src = $row['image_uri'];
				}

				echo '
					<tr class="border-top">
						<td><image src="'. $src .'" width="125px" height="125px"></td>
						<td><p>'. $row['package_name'] .'</p></td>
						<td class="num"><p>$'. number_format($row['package_price'], 2) .'</p></td>
						<td class="num"><p>'. $cart[$id] .'</p></td>
						<td class="num"><p>$'. number_format($total, 2) .'</p></td>
						<td><button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop'. $id .'"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg></button></td>
						<form method="post">
						<td><input type="submit" class="btn-close" data-bs-dismiss="modal" aria-label="Close" value=""></button></td>
						<input type="hidden" name="delete" value="'. $id .'">
						</form>
					</tr>';
				
				echo '
				<div class="modal fade" id="staticBackdrop'. $id .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Cart Item</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form method="post">
						<div class="modal-body">
							<p>Change quantity: <input type="number" step="1" name="qty" value="'. $qty .'" max="10" style="max-width: 50px;"></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<input type="hidden" name="id" value="'. $id .'">
							<input type="submit" class="btn btn-primary" value="Confirm"></button>
						</div>
						</form>
						</div>
					</div>
				</div>';
			}
			echo '
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td class="border-top">Subtotal: </td>
					<td class="num border-top">$'. number_format($allTotal, 2) .'</td>
				</tr>
			</tbody>
			</table>
			<a href="checkout.php"><button type="button" class="btn btn-primary">CHECKOUT</button></a>
			</div>';
		} else {
			echo '<h3>Cart is currently empty.</h3>
				</div></div></div>';
		}
	?>
	</div>
	</body>
<?php
	include('../includes/footer.html');
?>