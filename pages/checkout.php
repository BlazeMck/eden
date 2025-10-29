<?php
	$page_title = 'Checkout';
	include('../includes/header.html');
    require_once('../util/keys.php');
    // require_once('../vendor/autoload.php');
    require_once('../util/mysqli_connect.php');
    require_once('../util/constants.php');
    

    // $stripe = new \Stripe\StripeClient($S_SECRET);
    
?>
</head>
<body>
    <?php
        if (empty($_SESSION['cart'])) {
            echo '<br><br><p class="error">This page was accessed in error.</p>';
            include('../includes/footer.html');
            exit();
        }
    ?>
    <h1>Checkout</h1>
    <form method="post" id="form" action="../pages/complete.php">
        <div class="d-flex flex-direction-row" style="margin-top: 60px;">
            <div class="border p-2 mx-auto d-flex flex-direction-row" style="scale: 120%; width: 37%">
                <div style="width: 50%;">
                    <div id="cardInfo">
                        <h4>Payment Method:</h4>
                        <p>Credit Card: <input type="radio" name="method" value="cr" id="cr" checked> &nbsp;&nbsp; Gift Card: <input type="radio" name="method" value="gi" id="gi"></p>
                        <h4>Card Info:</h4>
                        <p>Card Number:<br>
                        <input type="text" name="cardNum" placeholder="xxxx-xxxx-xxxx-xxxx" maxlength="19"><br>
                        Exp. Date: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspCVC Code:<br>
                        <input type="text" name="expDate" placeholder="mm/yy" maxlength="5" size="8">&nbsp&nbsp&nbsp<input type="text" name="secNum" placeholder="xxx" maxlength="3" size="5" id="cvc"><br>
                        Name:<br>
                        <input type="text" name="name" placeholder="As Written On Card" maxlength="30" size="20" id="name">
                        </p>
                    </div>
                    <div id="billing">
                        <h4>Billing Address:</h4>
                            <?php
                            $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
                            $q = "SELECT * FROM addresses WHERE user_id = $id";
                            $r = @mysqli_query($dbc, $q);
                            if (mysqli_num_rows($r) == 1) {
                                $add = mysqli_fetch_array($r, MYSQLI_ASSOC);
                            }
                            echo '
                                <p>Street Address Line 1:<br>
                                <input type="text" name="bill_line_1" value="'. (isset($add['line_1']) ? $add['line_1'] : null) .'"><br>
                                Street Address Line 2:<br>
                                <input type="text" name="bill_line_2" value="'. (isset($add['line_2']) ? $add['line_2'] : null) .'"><br>
                                City<br>
                                <input type="text" name="bill_city" value="'. (isset($add['city']) ? $add['city'] : null) .'"><br>
                                Zipcode &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp State<br>
                                <input type="text" size=10 name="bill_zip" value="'. (isset($add['zip']) ? $add['zip'] : null) .'"> 
                                <input list="states" style="width: 70px;" name="bill_state" value="'. (isset($add['state']) ? $add['state'] : null) .'">
                                    <datalist id="states">
                                ';
                                    foreach($states as $state) {
                                        echo '<option value='. $state .'>';
                                    }
                                echo '
                                    </datalist><br>
                                    </p>';
                            ?>
                    </div>
                </div>
                <div class="ms-3" style="width: 50%;">
                    <div id="shipping">
                        <h4>Shipping Address:</h4>
                        <p style="text-align: right;">Billing Same As Shipping? <input type="checkbox" name="same" value="yes" id="same"></p>
                        <p style="text-align: right;">Set Address As Default? <input type="checkbox" name="save" value="yes" id="save" <?php if(!isset($_SESSION['user_id'])) {echo 'disabled';} ?>></p>
                        <?php
                        echo '
                            <p>Street Address Line 1:<br>
                            <input type="text" name="ship_line_1"><br>
                            Street Address Line 2:<br>
                            <input type="text" name="ship_line_2"><br>
                            City<br>
                            <input type="text" name="ship_city"><br>
                            Zipcode &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp State<br>
                            <input type="text" size=10 name="ship_zip"> <input list="states" style="width: 70px;" name="ship_state">
                                <datalist id="states">
                            ';
                                foreach($states as $state) {
                                    echo '<option value='. $state .'>';
                                }
                            echo '
                                </datalist><br>
                                </p>';
                        ?>
                    </div>
                    <p>Email: 
                    <input type="email" id="email" name="email" value="<?php isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                    </p>
                    <input type="submit" id="submit" class="btn btn-success w-100" value="Place Order" disabled>
                </div>
            </div>
            <div class="mx-auto" style="scale: 120%; width: 35%;">
                <p>
                    <div class="border rounded border-2 border-success-subtle p-1 mx-auto" style="background-color: gainsboro;">
                    <h4>Product:</h4>
                    <?php
                        $cart = null;
                        if (isset($_SESSION['cart'])) {
                            $cart = $_SESSION['cart'];
                        } else {
                            $cart = json_decode($_COOKIE['cart']);
                        }



            $filter = '';
			$num = 1;
			foreach ($cart as $id => $item) {
				$filter .= "package_id = $id";
				if ($num != count($cart)) {
					$filter .= " OR ";
				}
				$num++;
			}

			echo '
				<table class="mx-auto table table-borderless table-secondary table-sm">
					<thead>
						<tr>
							<th></th>
							<th>Package</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>';
			$q = "SELECT * FROM packages WHERE $filter";
			$r = @mysqli_query($dbc, $q);

			$subTotal = 0;
			$allQty = 0;
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$id = $row['package_id'];
				$qty = $cart[$id];
				$allQty += $qty;
				$total = $row['package_price'] * $qty;
				$subTotal += $total;

				$src = "../includes/media/cornucopia-temp-DONOTPUBLISH.jpg";

				if (file_exists($row['image_uri'])) {
					$src = $row['image_uri'];
				}

				echo '
					<tr class="border-top">
						<td><image src="'. $src .'" width="40px" height="40px"></td>
						<td><p>'. $row['package_name'] .'</p></td>
						<td class="num"><p>$'. number_format($row['package_price'], 2) .'</p></td>
						<td class="num"><p>'. $cart[$id] .'</p></td>
						<td class="num"><p>$'. number_format($total, 2) .'</p></td>
					</tr>';
			}
            $ship = 9.99;
            $rate = 0.0485;
            $tax = $subTotal * $rate;
            $allTotal = $tax + $subTotal;


			echo '
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td class="border-top border-dark">Subtotal: </td>
					<td class="num border-top border-dark">$'. number_format($subTotal, 2) .'</td>
				</tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Shipping: </td>
                    <td class="num">$'. number_format($ship, 2) .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Taxes: </td>
                    <td class="num">$'. number_format($tax, 2) .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total: </td>
                    <td class="num">$'. number_format($allTotal, 2) .'</td>
                </tr>
			</tbody>
			</table>
            <input type="hidden" name="subtotal" value='. $subTotal .'>
            <input type="hidden" name="total" value='. $allTotal .'>
			</div>';
                    ?>
                    </div>
                </p>
                
            </div>
        </div>
    </form>
    <script src="../util/checkout.js"></script>
<?php
	include('../includes/footer.html');
?>