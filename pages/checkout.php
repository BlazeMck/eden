<?php
	$page_title = '';
	include('../includes/header.html');
?>
</head>
<body>
	<h1>Checkout</h1>
	<div>
	<?php 
		$cart = null;
		if (isset($_SESSION['cart'])) {
			$cart = $_SESSION['cart'];
		} elseif (isset($_COOKIE['cart'])) {
			$cart = json_decode($_COOKIE['cart']);
		}
		
		if ($cart) {

		} else {
			echo '<h3>Cart is currently empty.</h3>';
		}
	?>
	</div>
<?php
	include('../includes/footer.html');
?>