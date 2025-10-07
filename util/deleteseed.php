<?php
	$page_title = 'Delete A Seed';
	include('../includes/header.html');
	echo '</head><body>';
	echo '<h1>Delete a Seed</h1>';

	if ($_SESSION['user_level'] != 0) {
		echo'<p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
		$id = $_GET['id'];
	} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
		$id = $_POST['id'];
	} else {
		echo '<p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	require_once('../util/mysqli_connect.php');

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$q = "SELECT p.package_id, p.package_name FROM packages AS p LEFT JOIN package_contents AS pc ON p.package_id=pc.package_id WHERE pc.seed_id=$id";
		$r = @mysqli_query($dbc, $q);
		if ($r) {
			echo 'Seed found in packages, please remove seed from the following packages before attempting to delete:';
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				echo '<p>Package '. $row['package_id'] .' - '. $row['package_name'] .'</p>';
			}
		} else {
			$q = "DELETE FROM seeds WHERE seed_id=$id LIMIT 1";
			$r = @mysqli_query($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) {
				
				echo '<p>The seed has been deleted.</p>';

			} else {
				echo '<p class ="error">The seed could not be deleted due to a system error.</p>';
				echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
			}
		}
		
	} else {
		$q = "SELECT seed_name FROM seeds WHERE seed_id=$id";
		$r = @mysqli_query($dbc, $q);

		if (mysqli_num_rows($r) == 1) {
			$row = mysqli_fetch_array($r, MYSQLI_NUM);
			echo '<p>Are you sure you want to delete <strong>'. $row[0] .'</strong>?</p>';
			echo '<form action="deleteseed.php" method="post">
			      <input type="radio" name="confirm" value="Yes"> Yes
				  <input type="radio" name="confirm" value="No"> No
				  <input type="submit" name="submit" value="Submit">
				  <input type="hidden" name="id" value="'. $id .'">
				  </form>';
		} else {
			echo '<p class="error">This page has been accessed in error.</p>';
		}
	}
	mysqli_close($dbc);
	include('../includes/footer.html');
?>