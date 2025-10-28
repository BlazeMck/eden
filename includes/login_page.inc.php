<?php

    // Include the header:
    $page_title = 'Login';
    include('header.html');

    echo '</head>
          <body>';

    // Print any error messages, if they exist:
    if (isset($errors) && !empty($errors)) {
        echo '<h1>Error!</h1>
        <p  class="error">The following error(s) occurred:<br>';
        foreach ($errors as $msg) {
            echo " - $msg<br>\n";
        }
        echo '</p><p>Please try again</p>';
    }

    // Display the form:
    ?>
    <div class="d-flex justify-content-center p-5">
    <div class="d-flex justify-content-center flex-column bg-success-subtle align-items-center p-5" style="max-width: 100%;">
        <h1>Login</h1><br>
        <form action="login.php" method="post" novalidate>
            <p>Email Address: <input type="email" name="email" size="20" maxlength="60" value=<?php echo isset($_POST['email']) ? '"'. $_POST['email'] .'"' : ""; ?>></p>
            <p>Password: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="password" name="pass" size="20" maxlength="20" style="margin-left: 2px;"></p><br><br>
            <input type="submit" name="submit" class="btn btn-info w-100 my-3" value="Login">
        </form>
    </div>
    </div>

<?php include('footer.html'); ?>