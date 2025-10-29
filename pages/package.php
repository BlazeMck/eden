<?php
    require_once('../util/mysqli_connect.php');
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $q = "SELECT * FROM packages WHERE package_id = $id";
        $r = @mysqli_query($dbc, $q);
        $rc = mysqli_num_rows($r);
        if ($rc != 1){
            mysqli_close($dbc);
            header("Location: index.php");
        } else {
            $package = mysqli_fetch_array($r, MYSQLI_ASSOC);
        }
    } else {
        mysqli_close($dbc);
        header("Location: index.php");
    }

	$page_title = 'Package - '. $package['package_name'];
	include('../includes/header.html');

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        $qty = $_POST['quantity'];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
        if ($_SESSION['cart'][$id] > 10) {
            $_SESSION['cart'][$id] = 10;
        }
        
    }
?>
</head>
<body>
    <?php 
        $src = '';

        if (file_exists($package['image_uri'])) {
            $src = $package['image_uri'];
        } else {
            $src = '../includes/media/cornucopia-temp-DONOTPUBLISH.jpg';
        }
        
        echo '
        <div class="d-flex flex-row p-3 justify-content-around">
            <div style="width: 700px; height: 550px; background-color: white;">
                <image src="'. $src .'" width="500" height="500" style="margin-left: 100px; margin-top: 25px;">
            </div>
            <div class="d-flex flex-column align-items-end border border-2 px-4" style="margin-left: 80px; width: 300px;">
                <h2>'. $package['package_name'] .'</h2>
                <h4 class="price">'. $package['package_price'] .'</h4>
                <p>'. $package['package_desc'] .'</p>
                <form action=package.php?id='. $id .' method=post class="my-4">
                    <p>Quantity: <input type="number" name="quantity" step=1 size=3 value=1 min=1 max=10></p>
                    <input type="submit" value="Add To Cart" class="btn btn-success w-100">
                </form>';
        echo '
            </div>
        </div>
        <div class="my-3">
            <h2>Package Details</h2>
            <h4>Description:</h4>
            <p>'. $package['package_desc'] .'</p>
            <h4>Contents:</h4>
            <table width="60%">
                <thead>
                    <tr>
                        <th>Seed</th>
                        <th>Blurb</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>';
                $q = "SELECT s.seed_name, s.seed_blurb, pc.seed_qty, s.seed_id FROM seeds AS s JOIN package_contents AS pc ON s.seed_id = pc.seed_id WHERE pc.package_id=$id";
                $r = @mysqli_query($dbc, $q);
                while ($row = mysqli_fetch_array($r)) {
                    echo '
                        <tr>
                            <td><a href="seed.php?id='. $row['seed_id'] .'">'. $row['seed_name'] .'</a></td>
                            <td>'. $row['seed_blurb'] .'</td>
                            <td>'. $row['seed_qty'] .'</td>
                        </tr>
                    ';
                }
        echo '
                </tbody>
            </table>
        </div>
        ';

        
    ?>
<?php
	include('../includes/footer.html');
?>