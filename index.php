<?php
	$page_title = 'Home';
	session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo 'Eden\'s Seed Reserve : '. $page_title; ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
	<link href="./css/sticky-footer-navbar.css" rel="stylesheet">
	<link href="./css/table.css" rel="stylesheet">
	<link href="./css/validation.css" rel="stylesheet">
	<link href="./css/webpage.css" rel="stylesheet">
	<nav class="navbar navbar-expand-lg bg-body-tertiary fancy" style="z-index: 1000;">
		<div class="container-fluid">
		  <a class="navbar-brand" href="./index.php" style="scale: 120%;"><image src="./includes/media/tree.png" height="35"></image>Eden's Seed Reserve</a>
		  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse ms-4 border-start border-3 ps-5" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0" style="scale: 120%;">
			  <li class="nav-item">
				<a class="nav-link" aria-current="page" href="./index.php">Home</a>
			  </li>
			  <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				  Products
				</a>
				<ul class="dropdown-menu" style="width: 380px">
				  <div class="d-flex flex-row">
					<?php
					require_once('./util/mysqli_connect.php');
					echo '<div class="d-flex flex-column">
						<li><a class="dropdown-item" href="./pages/packages.php"><strong>Packaged Seeds</strong></a></li>
						<div class="d-flex flex-column" style="flex-wrap: wrap; max-height: 300px;">';
						$q = "SELECT package_name, package_id FROM packages";
						$r = @mysqli_query($dbc, $q);
						$num = 0;
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							echo '<li><a class="dropdown-item" href="./pages/package.php?id='. $row['package_id'] .'">'. $row['package_name'] .'</a></li>';
							$num++;
							if ($num == 17) {
								echo '<li><a class="dropdown-item" href="./pages/packages.php">See All...</a></li>';
							}
						}
					echo '
						</div>
					</div>
					<div class="d-flex flex-column ms-2">
						<li><a class="dropdown-item" href="./pages/seeds.php"><strong>Seeds</strong></a></li>
						<div class="d-flex flex-column border-start" style="flex-wrap: wrap; max-height: 300px;">';
						$q = "SELECT seed_name, seed_id FROM seeds";
						$r = @mysqli_query($dbc, $q);
						$num = 1;
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							echo '<li><a class="dropdown-item" href="./pages/seed.php?id='. $row['seed_id'] .'">'. $row['seed_name'] .'</a></li>';
							$num++;
							if ($num == 17) {
								echo '<li><a class="dropdown-item" href="./pages/seeds.php">See All...</a></li>';
							}
						}
					echo '</div>
					</div>';
					?>
				  </div>
				</ul>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="./pages/gardening.php">Tips and Tricks</a>
			  </li>
			  <li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<?php echo isset($_SESSION['user_id']) ? $_SESSION['first_name'] : 'User'?>
				</a>
				<ul class="dropdown-menu">
					<?php
						if(isset($_SESSION['user_id'])) {
							echo '<li><a class="dropdown-item" href="./pages/user.php">Settings</a></li>';
							if ($_SESSION['user_level'] == 1) {
								echo '<li><a class="dropdown-item" href="./pages/orders.php">Orders</a></li>';
							} else {
								echo '<li><a class="dropdown-item" href="./pages/adminorders.php">Orders</a></li>';
								echo '<li><a class="dropdown-item" href="./pages/adminpackages.php">Packages</a></li>';
								echo '<li><a class="dropdown-item" href="./pages/adminseeds.php">Edit Seeds</a></li>';
								echo '<li><a class="dropdown-item" href="./pages/adminuser.php">Edit Users</a></li>';
							}
								echo '<li><a class="dropdown-item" href="./pages/signout.php">Signout</a></li>';
						} else {
							echo '
								<li><a class="dropdown-item" href="./pages/signup.php">Signup</a></li>
								<li><a class="dropdown-item" href="./pages/login.php">Login</a></li>
								<li><a class="dropdown-item" href="./pages/orders.php">Orders</a></li>';
						}
					?>
				</ul>
				
			  </li>
			  <li class="mx-2 mt-1">
				<a href="./pages/cart.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="black" class="bi bi-cart" viewBox="0 0 16 16">
				<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
				</svg>
				</a>
			  </li>
			</ul>
			
			<!-- <form class="d-flex" role="search">
			  <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
			  <button class="btn btn-outline-success" type="submit">Search</button>
			</form> -->
			
		  </div>
		</div>
	  </nav>
	  </head>
	<div class="container my-1">
<!-- Begin page content -->

</head>
<body>
	<div class="d-flex flex-column align-items-center mx-auto" style="scale: 125%; margin-top: 12rem; margin-bottom: 12rem; max-width: 70%;">
	<div class="page-header border-bottom border-3 border-warning-subtle"><h1>Eden's Seed Reserve</h1></div>

	<h2>Your Foundation for Ensuring Food Security</h2>
		<image src="./includes/media/gardening.jpg" width="700" height="400"></image><br>
	<h2>Secure Your Tomorrow. Grow Your Own Eden.</h2><br>
	
	
	<p>Eden's Seed Reserve provides the meticulously curated, non-GMO, heirloom seeds you need to ensure food security,
		regardless of what tomorrow brings. Whether you're a survivalist planning for the long term or a self-sufficient gardener,
		start your reserve here!
	</p>
	
	<p>
		Imagine a world where grocery shelves aren't guaranteed to be full. Global instability and supply chain disruptions are real risks – and preparation is key.
	</p>

	<p><strong>Eden's Seed Reserve</strong> empowers you to take control of your food security. We provide premium, non-GMO, heirloom seeds designed to keep your family and community resilient in uncertain times.</p>
    
	<h5 class="my-2" style="font-family: 'Jost', sans-serif; text-align: center;">Here's why you should trust Eden's Seed Reserve:</h5>
	<ul>
		<li><strong>Long-Term Assurance:</strong> Seeds last 3-5 years, and <strong>we'll remind you</strong> when it's time to refresh your supply.</li>
		<li><strong>Guaranteed Access:</strong> Register with us and stay connected for replacement opportunities (subject to availability and market pricing).</li>
		<li><strong>Peace of Mind:</strong> Always have a fresh seed reserve ready–because when supply chains fail, preparation wins.</li>
	</ul>
	<h4 style="font-family: 'Jost', sans-serif; text-align: center;">We address the need for long-term preparedness.</h4>

	<h3 class="my-2">Ultimate Seed Security</h3>

	<p>We believe true freedom starts with food independence. Our package is not just a collection of seeds;
		it is a life-sustaining investment, packaged for maximum shelf life and genetic diversity. In a crisis, your most valuable
		asset will be the ability to feed yourself and your community.
	</p>

	<h5 class="my-2" style="font-family: 'Jost', sans-serif; text-align: center;">Associated Goals</h5>

	<ul class="my-2">
		<li><strong>Long-Term Storage:</strong> Seeds packaged for three to five years of optimal viability.</li>
		<li><strong>Heirloom & Non-GMO:</strong> Seed-bearing crops. Plant, Harvest, Replant, Repeat.</li>
		<li><strong>Comprehensive Variety:</strong> Everything from caloric staples to nutrient-dense herbs.</li>
	</ul>

	<h3 class="my-2">Designed for Regional Success</h3>

	<p>Don't waste a season on guesswork. Our collections come with recommended climate zones, soil types, and growing seasons.
		We take the confusion out of what to plant, so you can focus on your garden without stress.
	</p>

	<h5 class="my-2" style="font-family: 'Jost', sans-serif; text-align: center;">Associated Goals</h5>

	<ul class="my-2">
		<li><strong>Geo-Specific Packages:</strong> Seeds guaranteed to thrive in your climate zone.</li>
		<li><strong>Curated for Yield:</strong> Focus on high-output, reliable crops.</li>
		<li><strong>Beginner-Friendly:</strong> Visit our "Tips and Tricks" page to learn how to start your fresh garden.</li>
	</ul>
    
	<h3 class="my-2">Why Eden's Seed Reserve?</h3>

	<ul class="my-2">
		<li><strong>100% Heirloom & Open-Pollinated:</strong> All seeds are open-pollinated, meaning you can harvest the seeds from your produce to plant them again next year.</li>
		<li><strong>Focus on Optimal Germination:</strong> We are committed to ensuring you get the highest rate of germination. Seeds will last about 3 years before their germination rates start to suffer. We will inform you when it's time for a new package.</li>
	</ul>
	<p class="my-2">Our goal is to help you feel at home in your garden, and ensure that what you plant grows fruitful and healthy!</p>
	</div>
<?php
	include('./includes/footer.html');
?>