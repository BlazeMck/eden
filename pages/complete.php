<?php
	$page_title = 'Completed';
	include('../includes/header.html');
    require_once('../util/mysqli_connect.php');
    
    function exitScript() {
        include('../includes/footer.html');
        exit();
    }
?>
</head>
<body>
    <h1 style="text-align: center;">Completed Order</h1>
    <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_SESSION['cart'])) {
            $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NULL";

            if (isset($_POST['same'])){
                $l1 = mysqli_real_escape_string($dbc, $_POST['bill_line_1']);
                $l2 = mysqli_real_escape_string($dbc, $_POST['bill_line_2']);
                $city = mysqli_real_escape_string($dbc, $_POST['bill_city']);
                $zip = mysqli_real_escape_string($dbc, $_POST['bill_zip']);
                $state = mysqli_real_escape_string($dbc, $_POST['bill_state']);
            } else {
                $l1 = mysqli_real_escape_string($dbc, $_POST['ship_line_1']);
                $l2 = mysqli_real_escape_string($dbc, $_POST['ship_line_2']);
                $city = mysqli_real_escape_string($dbc, $_POST['ship_city']);
                $zip = mysqli_real_escape_string($dbc, $_POST['ship_zip']);
                $state = mysqli_real_escape_string($dbc, $_POST['ship_state']);
            }

            $st = $_POST['subtotal'];
            $add = $l1 .' '. $l2;
            $email = mysqli_real_escape_string($dbc, $_POST['email']);

            if(isset($_POST['save']) && isset($_SESSION['user_id'])) {
                $q = "UPDATE addresses SET line_1 = '$l1', line_2 = '$l2', city = '$city', zip = $zip, state = '$state' WHERE user_id = $id";
                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) {
                }
            }
            

            $q = "INSERT INTO orders (customer_id, subtotal, delivery_address, delivery_city, delivery_zip, delivery_state, order_date, email) VALUES ($id, $st, '$add', '$city', $zip, '$state', NOW(), '$email')";
            $r = @mysqli_query($dbc, $q);
            if ($r) {
                $q = "SELECT order_id FROM orders WHERE email = '$email' AND order_date = (SELECT MAX(order_date) from orders WHERE email = '$email')";
                $r = @mysqli_query($dbc, $q);
                $order = mysqli_fetch_array($r, MYSQLI_ASSOC);
                $orderId = $order['order_id'];
                foreach ($_SESSION['cart'] as $id => $pro) {
                    $q = "INSERT INTO order_details(order_id, package_id, package_qty) VALUES($orderId, $id, $pro)";
                    $r = @mysqli_query($dbc, $q);
                    if (!$r) {
                        echo '<p class="error">Your order could not be completed due to a system error. Please try again</p>';
                        exitScript();
                    }
                }
            } else {
                echo '<p class="error">Your order could not be completed due to a system error. Please try again.</p>';
                exitScript();
            }
        } else {
            echo '<p class="error">This page was accessed in error.</p>';
            exitScript();
        }

echo '
    <div style="scale: 120%; max-width: 33%; background-color: #c1c995" class="p-4 mx-auto mt-5">
        <h4 class="border-bottom border-3">Order Details - '. $orderId .'</h4>
        <p class="border-bottom border-3">Customer Info:<br>
        <strong>'. (isset($_SESSION['first_name']) ? $_SESSION['first_name'] .' '. $_SESSION['last_name'] : 'Guest Account') .'</strong><br>
        '. $email .'<br>
        <strong>'. $add .'<br>'. $city .', 
        '. $zip .' '. $state .'</strong><br>
        </p>';

        $cart = $_SESSION['cart'];

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
    </div>
';
$_SESSION['cart'] = [];
    ?>
<?php
	include('../includes/footer.html');
?>