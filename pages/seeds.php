<?php
	$page_title = 'Seeds';
	include('../includes/header.html');
?>
</head>
<h1>Seeds</h1>
	<div class="container text-center" style="max-width: 1200">
		<?php 
			require_once('../util/mysqli_connect.php');

			$q = "SELECT * FROM seeds";
			$r = @mysqli_query($dbc, $q);
			$num = 0;
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

				$src = '';
				if (file_exists($row['image_uri'])) {
					$src = $row['image_uri'];
				} else {
					$src = "../includes/media/sprout.jpg";
				}
				
				if ($num % 4 == 0) {
					echo '<div class="row">';
				}
				echo '
					<div class="col border p-1" style="background-color: white; max-width: 250px; margin-left: 50px; margin-top: 25px;" onclick="location.href=\'seed.php?id='. $row['seed_id'] .'\'">
						<image src="'. $src .'" style="margin-bottom: 5px;" width="225px" height="225px">
						<p>'. $row['seed_name'] .' - '. $row['seed_blurb'] .'</p>
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
						<h3>MORE SEEDS COMING SOON</h3>
					</div>';
			}
		?>
	</div>
<?php
	include('../includes/footer.html');
?>