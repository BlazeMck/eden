<?php
	$page_title = 'Packages';
	include('../includes/header.html');
?>
</head>
<body>
	<h1>Packages</h1>
	<div class="text-center d-flex align-items-center">
		<div class="d-flex flex-wrap mx-auto">
		<?php 
			require_once('../util/mysqli_connect.php');

			$q = "SELECT * FROM packages";
			$r = @mysqli_query($dbc, $q);
			$num = 0;
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

				$src = '';
				if (file_exists($row['image_uri'])) {
					$src = $row['image_uri'];
				} else {
					$src = "../includes/media/cornucopia-temp-DONOTPUBLISH.jpg";
				}
				echo '
					<div class="col border p-1 mx-4" id="package" style="background-color: white; max-width: 250px; margin-top: 25px;" onclick="location.href=\'package.php?id='. $row['package_id'] .'\'">
						<image src="'. $src .'" style="margin-bottom: 5px;">
						<p>'. $row['package_name'] .' - $'. $row['package_price'] .'</p>
					</div>
				';
			}

				echo '
					<div class="flex col border p-1 align-content-center mx-4" style="background-color: gray; max-width: 250px; height: 280px; margin-top: 25px;">
						<h3>MORE PACKAGES COMING SOON</h3>
					</div>';
		?>
		</div>
	</div>
</div>
<?php
	include('../includes/footer.html');
?>