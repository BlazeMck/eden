<?php
	$page_title = 'Checkout';
	include('../includes/header.html');
    require_once('../util/keys.php');
    require_once('../vendor/autoload.php');
    
    $stripe = new \Stripe\StripeClient($S_SECRET);
    
?>
</head>
<body>
    <h1>Checkout</h1>
    <?php
        $session = $stripe->checkout->sessions->create([
            'ui_mode' => 'custom',
            'line_items' => [
            [
                'price' => 'price_1SKoXs2OAZI8szDyxuzY0pqr',
                'quantity' => 1,
            ]
            ],
            'mode' => 'payment',
            'return_url' => '../pages/complete.php',
        ]);
    ?>
<?php
	include('../includes/footer.html');
?>