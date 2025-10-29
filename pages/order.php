<?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        header("Location: ../pages/orders.php");
    }

    require_once('../util/mysqli_connect.php');

    $q = "SELECT * FROM orders WHERE order_id = $id";
    $r = @mysqli_query($dbc, $q);
    $order = mysqli_fetch_array($r, MYSQLI_ASSOC);

    

    $page_title = "Order Details";
    include('../includes/header.html');
    
    if (((isset($_SESSION['user_id']) && $order['customer_id'] == $_SESSION['user_id']) || (is_null($order['customer_id']))) || (isset($_SESSION['user_id']) && $_SESSION['user_level'] == 0)){
    } else {
        echo '</head><body><p class="error">This page has been accessed in error.</p>';
		include('../includes/footer.html');
		exit();
    }
?>
</head>
<body>
    <?php
        echo "<h1>Order Details - Order #$id</h1>";

        $q = "SELECT o.customer_id AS id, o.delivery_address AS ad, 
                    o.delivery_city AS city, o.delivery_state AS st, 
                    o.delivery_zip AS zip, o.email AS oemail, u.email AS uemail, u.first_name, u.last_name, u.phone FROM orders AS o
                    LEFT JOIN users AS u ON o.customer_id = u.user_id WHERE o.order_id = $id";
        $r = @mysqli_query($dbc, $q);
        $details = mysqli_fetch_array($r, MYSQLI_ASSOC);

        $custinfo = '';
        if (isset($details['id'])) {
            $custinfo .= '<p>Placed By: '. $details['first_name'] .' '. $details['last_name'] .'
                            <br>Email: '. $details['uemail'] .'
                            <br>Phone: '. $details['phone'] .'</p>
                            <h3 class="border-bottom">Shipping Details:</h3>
                            <p>'. $details['ad'] .' '. $details['city'] .', '. $details['st'] .' '. $details['zip'] .'</p>';
        } else {
            $custinfo .= 'Placed By: Guest Account
                          <h3 class="border-bottom">Shipping Details:</h3>';
                if (!empty($_SESSION['user_level']) && $_SESSION['user_level'] == 0) {
                    $custinfo .= '<p>'. $details['ad'] .' '. $details['city'] .', '. $details['st'] .' '. $details['zip'] .'</p>';
                } else {
                    $custinfo .= '<p>All shipping details can be found within the Email sent to the Email address provided at time of checkout. If you require additional assistance in regards to your order, please contact a system administrator.</p>';  
                }   
        }

        echo '
        <div class="d-flex flex-direction-row justify-content-center">
        <div class="border p-2" style="width: 45%; max-width: 45%;">
            <h3 class="border-bottom">Customer Details:</h3>
             '. $custinfo .'
        </div>
        <div class="border p-2">
            <h3 class="border-bottom">Order Contents:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Quantity</th>
                            <th></th>
                            <th>Product Name</th>
                            <th></th>
                            <th>Product Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            ';


            $q = "SELECT p.package_name, p.image_uri, p.package_price, p.package_id, o.package_qty FROM order_details AS o JOIN packages AS p ON o.package_id = p.package_id WHERE order_id=$id";
            $r = @mysqli_query($dbc, $q);
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                echo '
                    <tr>
                        <td>'. $row['package_qty'] .'X</td>
                        <td><image src="'. $row['image_uri'] .'" width=50 height=50></td>
                        <td><a href="../pages/package.php?id='. $row['package_id'] .'">'. $row['package_name'] .'</a></td>
                        <td>@</td>
                        <td>$'. number_format($row['package_price'], 2) .'</td>
                        <td>$'. number_format($row['package_price'] * $row['package_qty'], 2) .'</td>
                    </tr>';
            }
        echo '</tbody></table></div></div>'
    ?>
<?php
    include('../includes/footer.html');
?>