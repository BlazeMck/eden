<?php
	$page_title = 'User Page';
	include('../includes/header.html');
?>
</head>
<body>
	<h1><?php echo $_SESSION['first_name']; ?></h1>
	<p>This page will include settings and the ability to change information for the user currently logged in.</p>
<?php
	include('../includes/footer.html');
?>