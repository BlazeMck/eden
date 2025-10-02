<?php

    $page_title = 'Activate Your Account';
    include ('../includes/header.html');

    if (isset($_GET['email'], $_GET['id'])
    && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)
    && (strlen($_GET['id']) == 32 ) ) {

        require_once('../util/mysqli_connect.php');
        $q = "UPDATE users SET active=NULL WHERE
            (email='" . mysqli_real_escape_string($dbc, $_GET['email']) ."' AND
            active='" . mysqli_real_escape_string($dbc, $_GET['id']) ."')
            LIMIT 1";
        $r = mysqli_query($dbc, $q) or die("Query: $q\n<br>MySQL Error: " .mysqli_error($dbc));

        if (mysqli_affected_rows($dbc) == 1) {
            echo "<h3>Your account is now active. You may now log in.</h3>";
        }
        else {
            echo '<p class="error">Your account could not be activated. Please re-check the link or contact a system administrator.</p>';
        }

        mysqli_close($dbc);
    } else {
        $url = 'http://localhost/eden/pages/index.php';
        header("Location: $url");
        exit();
    }

include('../includes/footer.html');
?>