<?php
	$page_title = 'Checkout';
	include('../includes/header.html');
    require_once('../util/keys.php');
    $stripe = new \Stripe\StripeClient($S_SECRET);
?>
</head>
<body>
    <h1>Checkout</h1>
    <?php
        $session = $stripe->checkout->sessions->create([
            'line_items' => [
            [
                'price' => '15.99',
                'quantity' => 1,
            ]
            ]
        ])
    ?>
<?php
	include('../includes/footer.html');
?>