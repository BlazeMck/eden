<?php 

function redirect_user($page = 'index.php') {

    $url = 'http://'. $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

    $url = rtrim($url, '/\\');

    $url .= '/' . $page;

    header("Location: $url");
    exit(); 
} // End of the redirect_user() function.

function check_login($dbc, $email = '', $pass = '') {

    $errors = []; // Initialize error array.

    // Validate the email address:
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email address.';
    } else {
        $e = mysqli_real_escape_string($dbc, trim($_POST['email']));
        
        // Check if email address is valid format:
        $epattern = '/\b[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}\b/';
        if (!preg_match($epattern, $e)) {
            $errors[] = 'Email is not correctly formatted.';
        }
    }

    // Validate the password:
    if (empty($pass)) {
        $errors[] = 'You forgot to enter your password.';
    } else {
        $p = mysqli_real_escape_string($dbc, trim($pass));
    }

    if (empty($errors)) {  // If everything's OK.

        // Retrieve the user_id and first_name for that email/password combination:
            $q = "SELECT user_id, first_name, last_name, email, user_level FROM users WHERE email='$e' AND pass=SHA2('$p', 512) AND active IS NULL";
            $r = @mysqli_query($dbc, $q); // Run the query.

            // Check the result:
            if (mysqli_num_rows($r) == 1) {

                // Fetch the record:
                $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

                // return true and the record:
                return [true, $row];

            } else { // Not a match!
                $errors[] = 'The email address and password entered do not match those on file, or your account is not activated.';
            }

    } // End of empty($errors) IF.

    // Return false and the errors:
    return [false, $errors];

} // End of check_login() function