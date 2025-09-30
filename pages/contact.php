<?php
	$page_title = '';
	include('../includes/header.html');
?>
</head>
<body>
	<h2>Contact Us</h2>
	<p>Please type your comments here.</p>
	<form action="contact.php" method="post">
		<textarea name="comments" rows="5" cols="40"><?php if (isset($_POST['comments'])) echo $_POST['comments']; ?></textarea>
		<p><input type="submit" name="submit" value="Send!"></p>
	</form>
		
	</form>
<?php
	include('../includes/footer.html');
?>