<?php
	require_once('../util/mysqli_connect.php');
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $q = "SELECT * FROM seeds WHERE seed_id = $id";
        $r = @mysqli_query($dbc, $q);
        $rc = mysqli_num_rows($r);
        if ($rc != 1){
            mysqli_close($dbc);
            header("Location: index.php");
        } else {
            $seed = mysqli_fetch_array($r, MYSQLI_ASSOC);
        }
    } else {
        mysqli_close($dbc);
        header("Location: index.php");
    }

	$page_title = 'Seed - '. $seed['seed_name'];
	include('../includes/header.html');
?>
</head>
<body>
	<?php
        $src = '';
        if (file_exists($seed['image_uri'])) {
            $src = $seed['image_uri'];
        } else {
            $src = '../includes/media/sprout.jpg';
        }

		echo '<h1>'. $seed['seed_name'] .' Seed</h1>';
		echo '
		<div class="d-flex flex-row p-3 justify-content-around">
            <div style="width: 700px; height: 550px; background-color: white;">
                <image src="'. $src .'" width="500" height="500" style="margin-left: 100px; margin-top: 25px;">
            </div>
			<div class="d-flex flex-column align-items-end border border-2 px-4" style="margin-left: 80px; width: 300px;">
				<h2>'. $seed['seed_name'] .'</h2>
				<h4>'. $seed['seed_blurb'] .'</h4>
				<p>'. $seed['seed_desc'] .'</p>
			</div>
		</div>
		<div class="my-3">
            <h2>Seed Details</h2>
            <h4>Containing Packages:</h4>
            <table width="60%">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Price</th>
                        <th>Containing Quantity</th>
                    </tr>
                </thead>
                <tbody>';
                $q = "SELECT p.package_name, p.package_price, pc.seed_qty, p.package_id FROM packages AS p JOIN package_contents AS pc ON p.package_id = pc.package_id WHERE pc.seed_id=$id";
                $r = @mysqli_query($dbc, $q);
                while ($row = mysqli_fetch_array($r)) {
                    echo '
                        <tr>
                            <td><a href="package.php?id='. $row['package_id'] .'">'. $row['package_name'] .'</a></td>
                            <td>'. $row['package_price'] .'</td>
                            <td>'. $row['seed_qty'] .'</td>
                        </tr>
                    ';
                }
        echo '
                </tbody>
            </table>
			<a href="../pages/gardening.php"><h4>Tips and Tricks</h4></a>';
			// <h4>When To Plant</h4>
			// <h4>How To Care For</h4>
			// <h4>How To Harvest</h4>
			// <h4>What To Do After</h4>
        echo '
        </div>
        ';
	?>

<?php
	include('../includes/footer.html');
?>