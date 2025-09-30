<?php
	$page_title = 'Orders';
	include('../includes/header.html');
?>
</head>
<body>
	<?php 
		if (isset($_SESSION['user_id'])) {
			echo '<h1>Orders - '. $_SESSION['first_name'] .'</h1>
				  <p>This page will be used to list the orders placed by the user currently logged in.</p>';
		} else {
			echo '<h1>Orders - No User</h1>
				  <p>This page will allow users to find and redirect to an order details page so long as they have the order number if they are not logged in.</p>';
		}
	
		include('../includes/footer.html');
	?>