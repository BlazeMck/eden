<?php
	$page_title = 'Admin Packages';
	include('../includes/header.html');
?>
</head>
<body>
<?php
	echo '<h1>Packages</h1>';

	if ($_SESSION['user_level'] != 0) {
		echo '<p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
	}

	require_once('../util/mysqli_connect.php');

	$q = "SELECT seed_id, seed_name FROM seeds";
	$r = @mysqli_query($dbc, $q);
	$phpData = [];
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$newData = ['id' => $row['seed_id'], 'name' => $row['seed_name']];
		array_push($phpData, $newData);
	}
	$jsonData = json_encode($phpData);

	

	$display = 5;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	 	if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
		
			$id = $_POST['delete'];

			$q = "DELETE FROM package_contents WHERE package_id = $id";
			$r = @mysqli_query($dbc, $q);
			$q = "DELETE FROM packages WHERE package_id = $id";
			$r = @mysqli_query($dbc, $q);

		} elseif (isset($_POST['edit']) && is_numeric($_POST['edit'])) {

			$errors = [];

			$id = $_POST['edit'];

			$target_file = null;

			if (isset($_POST['name'])){
				$name = $_POST['name'];
			} else {
				$errors[] = "A package name is required.";
			}

			if (isset($_POST['price'])) {
				$price = $_POST['price'];
			} else {
				$errors[] = "A package price is required.";
			}

			if (isset($_POST['desc'])) {
				$desc = $_POST['desc'];
			} else {
				$errors[] = "A package description is required.";
			}

			if (isset($_POST['seed1'], $_POST['seed2'], $_POST['seed3'], $_POST['seed4'], $_POST['seed5'])) {
				$content = [['id' => $_POST['seed1'], 'qty' => $_POST['quantity1']],['id' => $_POST['seed2'], 'qty' => $_POST['quantity2']],['id' => $_POST['seed3'], 'qty' => $_POST['quantity3']],['id' => $_POST['seed4'], 'qty' => $_POST['quantity4']],['id' => $_POST['seed5'], 'qty' => $_POST['quantity5']]];
			} else {
				$errors[] = "All package contents are required.";
			}

			$q = "SELECT * FROM packages WHERE package_id = $id";
			$r = @mysqli_query($dbc, $q);
			$rc = mysqli_num_rows($r);

			if ($rc == 1) {
				$package = mysqli_fetch_array($r, MYSQLI_ASSOC);
				$target_file = $package['image_uri'];
			

				if (isset($_FILES['upload']) && file_exists($_FILES['upload']['tmp_name'])) {
					$target_dir = "../includes/media/";
					$target_file = $target_dir . basename($_FILES["upload"]["name"]);
					$uploadOk = 1;
					$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					if(isset($_POST["submit"])) {
					$check = getimagesize($_FILES["upload"]["tmp_name"]);
					if($check !== false) {
						$uploadOk = 1;
					} else {
						$errors[] = "File is not an image.";
						$uploadOk = 0;
					}
					}

					$q = "SELECT * FROM packages WHERE image_uri = '$target_file'";
					$r = @mysqli_query($dbc, $q);
					if (mysqli_num_rows($r) > 0) {
						$errors[] = "Sorry, you are trying to replace a file with the same name that is in use by a different resource, please consider renaming this file to something different.";
						$uploadOk = 0;
					}

					// Check file size
					if ($_FILES["upload"]["size"] > 500000) {
						$errors[] = "Sorry, your file is too large.";
						$uploadOk = 0;
					}

					// Allow certain file formats
					if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
					&& $imageFileType != "gif" ) {
						$errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
						$uploadOk = 0;
					}

					// Check if $uploadOk is set to 0 by an error
					if ($uploadOk == 0) {
						$errors[] = "Sorry, your file was not uploaded.";
					// if everything is ok, try to upload file
					} else {
						if (!move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
							$errors[] = "Sorry, there was an error uploading your file.";
						}
					}
				}
			} else {
				$errors[] = "Selected package does not exist.";
			}

			if ($package['package_name'] == $name && $package['package_price'] == $price && $package['package_desc'] == $desc && ($package['image_uri'] == $target_file || !isset($target_file))) {
				$errors[] = "No changes made to selected package.";
			}

			if (!$errors) {
				$q = "UPDATE packages SET package_name = '$name', package_desc = '$desc', package_price = $price, image_uri = '$target_file' WHERE package_id = $id";
				$r = @mysqli_query($dbc, $q);
				if (mysqli_affected_rows($dbc) == 1) {
					echo "<p>The package - $name - has been updated.</p>";

					$q = "DELETE FROM package_contents WHERE package_id = $id";
					$r = @mysqli_query($dbc, $q);

					$q = "INSERT INTO package_contents(package_id, seed_id, seed_qty) VALUES ";
					foreach($content as $entry) {
						$q .= '('. $id .', '. $entry['id'] .', '. $entry['qty'] .'), ';
					}
					$q = rtrim($q, ', ');
					$r = @mysqli_query($dbc, $q);
					if (mysqli_affected_rows($dbc) > 0) {
						echo "<p>The contents of package - $name - has been updated.</p>";
					} else {
						echo '<p class="error">The package contents could not be updated due to a system error.</p>';
						echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
					}
				} else {
					echo '<p class="error">The package could not be updated due to a system error.</p>';
					echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
				}
			} else {
				foreach ($errors as $error) {
					echo '<p class="error">'. $error .'</p>';
				}
			}
		} elseif (isset($_POST['add']) && is_numeric($_POST['add'])) {

			$target_file = null;
			$errors = [];

			if (isset($_POST['name'])) {
				$name = $_POST['name'];
			} else {
				$errors[] = "A package name is required.";
			}

			if (isset($_POST['price'])) {
				$price = $_POST['price'];
			} else {
				$errors[] = "A package price is required.";
			}

			if (isset($_POST['desc'])) {
				$desc = $_POST['desc'];
			} else {
				$errors[] = "A package price is required.";
			}

			if (isset($_POST['seed1'], $_POST['seed2'], $_POST['seed3'], $_POST['seed4'], $_POST['seed5'])) {
				$content = [['id' => $_POST['seed1'], 'qty' => $_POST['quantity1']],['id' => $_POST['seed2'], 'qty' => $_POST['quantity2']],['id' => $_POST['seed3'], 'qty' => $_POST['quantity3']],['id' => $_POST['seed4'], 'qty' => $_POST['quantity4']],['id' => $_POST['seed5'], 'qty' => $_POST['quantity5']]];
			} else {
				$errors[] = "All package contents are required.";
			}

			if (isset($_FILES['upload']) && file_exists($_FILES['upload']['tmp_name'])) {
				$target_dir = "../includes/media/";
				$target_file = $target_dir . basename($_FILES["upload"]["name"]);
				$uploadOk = 1;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {
				$check = getimagesize($_FILES["upload"]["tmp_name"]);
				if($check !== false) {
					$uploadOk = 1;
				} else {
					$errors[] = "File is not an image.";
					$uploadOk = 0;
				}
				}

				$q = "SELECT * FROM packages WHERE image_uri = '$target_file'";
				$r = @mysqli_query($dbc, $q);
				if (mysqli_num_rows($r) > 0) {
					$errors[] = "Sorry, you are trying to replace a file with the same name that is in use by a different resource, please consider renaming this file to something different.";
					$uploadOk = 0;
				}

				// Check file size
				if ($_FILES["upload"]["size"] > 500000) {
					$errors[] = "Sorry, your file is too large.";
					$uploadOk = 0;
				}

				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					$errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
				}

				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					$errors[] = "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
					if (!move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
						$errors[] = "Sorry, there was an error uploading your file.";
					}
				}
			}

			if (!$errors) {
				$q = "INSERT INTO packages(package_name, package_price, package_desc, image_uri) VALUES ('$name', $price, '$desc', '$target_file')";
				$r = @mysqli_query($dbc, $q);

				$q = "SELECT package_id FROM packages WHERE package_name='$name' AND package_desc='$desc'";
				$r = @mysqli_query($dbc, $q);
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$id = $row[0];
				if (mysqli_affected_rows($dbc) == 1) {
					echo "<p>The package - $name - has been added.</p>";

					$q = "INSERT INTO package_contents(package_id, seed_id, seed_qty) VALUES ";
					foreach($content as $entry) {
						$q .= '('. $id .', '. $entry['id'] .', '. $entry['qty'] .'),';
					}
					$q = rtrim($q, ', ');
					$r = @mysqli_query($dbc, $q);
					if (mysqli_affected_rows($dbc) > 0) {
						echo "<p>The contents of package - $name - has been added.</p>";
					} else {
						echo '<p class="error">The package contents could not be added due to a system error.</p>';
						echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
					}
				} else {
					echo '<p class="error">The package could not be updated due to a system error.</p>';
					echo '<p>'. mysqli_error($dbc) .'<br>Query: '. $q .'</p>';
				}
			} else {
				foreach ($errors as $error) {
					echo '<p class="error-md">'. $error .'</p>';
				}
			}
		}
	}

	if (isset($_GET['p']) && is_numeric($_GET['p'])) {

		$pages = $_GET['p'];

	} else {

		$q = "SELECT COUNT(package_id) FROM packages";
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
			$order_by = "package_id ASC";
			break;
		case 'idd':
			$order_by = "package_id DESC";
			break;
		case 'pna':
			$order_by = "package_name ASC";
			break;
		case 'pnd':
			$order_by = "package_name DESC";
			break;
		case 'ppa':
			$order_by = "package_price ASC";
			break;
		case 'ppd':
			$order_by = "package_price DESC";
			break;
		default:
			$order_by = "package_id ASC";
			$sort = 'ida';
			break;

	}

	$q = "SELECT * FROM packages ORDER BY $order_by LIMIT $start, $display";
	$r = @mysqli_query($dbc, $q);

	echo '
		<table width="60%" class="left">
			<thead>
				<tr>
					<th>Edit</th>
					<th>Delete</th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_id ASC" ? "idd" : "ida") .'">Package ID</a></th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_name ASC" ? "pnd" : "pna") .'">Package Name</a></th>
					<th><a href="adminpackages.php?sort='. ($order_by == "package_price ASC" ? "ppd" : "ppa") .'">Package Price</a></th>
					<th>Contents</th>
				</tr>
			</thead>
			<tbody>';

	$bg = '#eeeeee';

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		
		echo '
			<tr bgcolor="'. $bg .'">
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropEdit'. $row['package_id'] .'">Edit</button></td>
				<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdropDelete'. $row['package_id'] .'">Delete</button></td>
				<td>'. $row['package_id'] .'</td>
				<td>'. $row['package_name'] .'</td>
				<td>'. $row['package_price'] .'</td>
				<td>';
			$id = $row['package_id'];
			$q = "SELECT s.seed_name, s.seed_id, p.package_id, pc.seed_qty FROM packages AS p JOIN package_contents AS pc ON p.package_id = pc.package_id JOIN seeds AS s ON s.seed_id=pc.seed_id WHERE p.package_id = $id";
			$result = @mysqli_query($dbc, $q);
			$packageContents = [];
			while ($cRow = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				echo '<p>'. $cRow['seed_id'] .': '. $cRow['seed_name'] .' - Qty '. $cRow['seed_qty'] .'</p>';
				array_push($packageContents, ['id' => $cRow['seed_id'], 'name' => $cRow['seed_name'], 'qty' => $cRow['seed_qty']]);
			}
			echo '</td></tr>';

			echo '

			<!-- Edit Modal -->
			<div class="modal fade" id="staticBackdropEdit'. $row['package_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Package - '. $row['package_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="modal-body">
					<p>Name: <input type="text" name="name" value="'. (isset($row['package_name']) ? $row['package_name'] : null) .'"></p>
					<p>Price: <input type="number" step=0.01 name="price" value="'. (isset($row['package_price']) ? $row['package_price'] : null) .'"></p>
					<p>Description:</p> <textarea name="desc" rows="5" cols="40">'. (isset($row['package_desc']) ? $row['package_desc'] : null) .'</textarea>
					<div id="content_select">';
						for ($i = 0; $i < 5; $i++) {
							$currentIndex = $i + 1;
							isset($packageContents[$i]) ? $seed = $packageContents[$i] : '';
							echo '
								<p>
									<select id="seed'. $currentIndex .'" name="seed'. $currentIndex .'">';
										foreach($phpData as $listData) {
											echo '<option value="'. $listData['id'] .'"';
											if (isset($seed)){
												if ($listData['id'] == $seed['id']) {
													echo 'selected';
												}
											}
											echo '>'. $listData['name'] .'</option>';
										}
							echo '
									</select>
									<input type=number step=1 name="quantity'. $currentIndex .'" size=3 value='. $seed['qty'] .'>
								</p>';
						}
				echo '
					</div>
					<p>Image: <input type="file" name="upload"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="edit" value='. $row['package_id'] .'>
				</div>
				</form>
				</div>
			</div>
			</div>
		';


		echo '

			<!-- Delete Modal -->
			<div class="modal fade" id="staticBackdropDelete'. $row['package_id'] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Package - '. $row['package_name'] .'</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete entry for <strong>'. $row['package_name'] .'</strong>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<form method="post">
						<input type="submit" name="submit" value="DELETE" class="btn btn-primary">
						<input type="hidden" name="delete" value='. $row['package_id'] .'>
					</form>
				</div>
				</div>
			</div>
			</div>
		';
	}

	echo '</tbody></table>';

	mysqli_free_result($r);
	mysqli_close($dbc);

	echo '<br><p>';
	if ($pages > 1) {

		$current_page = ($start/$display) + 1;

		echo '<div class="d-flex">';
		if ($current_page != 1) {
			echo '<a href="adminpackages.php?s='. ($start - $display) .'&p='. $pages .'&sort='. $sort .'" class="btn btn-secondary">Previous</a> ';
		}

		for ($i = ($current_page - 2 < 1 ? 1 : $current_page - 2); $i <= ($current_page + 2 > $pages ? $pages : $current_page + 2); $i++) {
			if ($i != $current_page) {
				echo '<a href="adminpackages.php?s='. (($display*($i-1))) .'&p='. $pages .'&sort='. $sort .'" class="btn btn-secondary">'. $i .'</a> ';
			} else {
				echo '<span class="btn btn-primary">'.$i .'</span>';
			}
		}

		if ($current_page != $pages) {
			echo '<a href="adminpackages.php?s='. ($start + $display) .'&p='. $pages .'$sort='. $sort .'" class="btn btn-secondary">Next</a>';
		}
		echo '</div>';
	}
	echo '</p>';
	echo '
			<button type="button" class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#staticBackdropAdd">Add New Package</button>

			<!-- Add Modal -->
			<div class="modal fade" id="staticBackdropAdd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Add Package</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" enctype="multipart/form-data">
				<div class="modal-body">
					<p>Name: <input type="text" name="name"></p>
					<p>Price: <input type="number" step=0.01 name="price"></p>
					<p>Description:</p> <textarea name="desc" rows="5" cols="40"></textarea>
					<div id="content_select">';
						for ($i = 0; $i < 5; $i++) {
							$currentIndex = $i + 1;
							echo '
								<p>
									<select id="seed'. $currentIndex .'" name="seed'. $currentIndex .'">';
										foreach($phpData as $listData) {
											echo '<option value="'. $listData['id'] .'">'. $listData['name'] .'</option>';
										}
							echo '
									</select>
									<input type=number step=1 name="quantity'. $currentIndex .'" size=3>
								</p>';
						}
				echo '
					</div>
					<p>Image: <input type="file" name="upload"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<input type="submit" name="submit" value="CONFIRM" class="btn btn-primary">
						<input type="hidden" name="add" value=1>
				</div>
				</form>
				</div>
			</div>
			</div>
		';
?>
			
</div>

<?php
	include('../includes/footer.html');
?>