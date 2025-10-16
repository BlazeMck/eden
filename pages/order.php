<?php
	$page_title = 'Packages';
	include('../includes/header.html');
?>
</head>
<body>
	<h1>Packages</h1>
	<div class="container text-center" style="max-width: 1200">
		<?php 
			require_once('../util/mysqli_connect.php');

			$q = "SELECT * FROM packages";
			$r = @mysqli_query($dbc, $q);
			$num = 0;
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$position = '';

				switch ($num % 4) {
					case 0:
						break;
					case 1:
						break;
					case 2:
						break;
					case 3:
						break;
					default:
						break;

				}
				
				if ($num % 4 == 0) {
					echo '<div class="row">';
				}
				echo '
					<div class="col border p-1" style="background-color: white; max-width: 250px; margin-left: 50px; margin-top: 25px;" onclick="location.href=\'package.php?id='. $row['package_id'] .'\'">
						<image src="../includes/media/cornucopia-temp-DONOTPUBLISH.jpg" style="margin-bottom: 5px;">
						<p>'. $row['package_name'] .' - $'. $row['package_price'] .'</p>
					</div>
				';
				if ($num % 4 == 3) {
					echo '</div>';
				}
				$num++;
			}

			if ($num %4 != 0) {
				echo '
					<div class="flex col border p-1 align-content-center" style="background-color: gray; max-width: 250px; height: 280px; margin-left: 50px; margin-top: 25px;">
						<h3>MORE PACKAGES COMING SOON</h3>
					</div>';
			}
		?>
	</div>
<?php
	include('../includes/footer.html');
?>