<?php
	$page_title = 'Completed';
	include('../includes/header.html');
    require_once('../util/mysqli_connect.php');
?>
</head>
<body>
    <h1>Completed Order</h1>
    <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (isset($_POST['same'])){
                $l1 = $_POST['bill_line_1'];
                $l2 = $_POST['bill_line_2'];
                $city = $_POST['bill_city'];
                $zip = $_POST['bill_zip'];
                $state = $_POST['bill_state'];
            } else {
                $l1 = $_POST['ship_line_1'];
                $l2 = $_POST['ship_line_2'];
                $city = $_POST['ship_city'];
                $zip = $_POST['ship_zip'];
                $state = $_POST['ship_state'];
            }

            $st = $_POST['subtotal'];
            $add = $l1 .' '. $l2;
            $email = $_POST['email'];

            if(isset($_POST['save']) && isset($_SESSION['user_id'])) {
                $q = "UPDATE addresses SET line_1 = $l1, line_2 = $l2, city = $city, zip = $zip, state = $state WHERE user_id = $id";
                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) {
                    print "Address successfully changed.";
                }
            }
            print_r($_POST);
            $q = "INSERT INTO orders(customer_id, subtotal, delivery_address, delivery_city, delivery_zip, order_date, email) VALUES($id, '$st', '$add', '$city', $zip, NOW(), '$email')";
            $r = @mysqli_query($dbc, $q);
            if ($r) {
                $q = "SELECT order_id, MAX(order_date) FROM orders WHERE customer_id = $id";
                $r = @mysqli_query($dbc, $q);
                $order = mysqli_fetch_array($r, MYSQLI_ASSOC);
                $orderId = $order['order_id'];
                foreach ($_SESSION['cart'] as $id => $pro) {
                    $q = "INSERT INTO order_details(order_id, package_id, package_qty) VALUES($orderId, $id, $pro)";
                    $r = @mysqli_query($dbc, $q);
                    if ($r) {
                        print "Order details $id => $pro for $orderId successful";
                    } else {
                        print "Order details $id => $pro for $orderId failed";
                        break;
                    }
                }
            } else {
                echo '<p class="error">Your order could not be completed due to a system error. Please try again.</p>';
            }
        }
    ?>
<?php
	include('../includes/footer.html');
?>