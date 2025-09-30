<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {

        require('../includes/login_functions.inc.php');
        redirect_user();

    } else {
        
        $first_name = $_SESSION['first_name'];
        $_SESSION = [];
        session_destroy();
        setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
    }

    $page_title = 'Logged Out!';
    include('../includes/header.html');

    echo "<h1>You have been sucessfully logged out $first_name.</h1>";

    include('../includes/footer.html');
?>