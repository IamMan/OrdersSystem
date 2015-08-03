<?php

include('layout.php');

define('LOGIN', 'login');
define('PASSWORD', 'password');
session_start();
$invalid_login = null;
if (isset($_SESSION[ACCOUNT_SESSION_NAME])) {
    header("Location: index.php");
    return;
}

if (!empty($_POST)) {
    if (isset($_POST[LOGIN])  and isset($_POST[PASSWORD])) {
        if (!empty($_POST[LOGIN]) and !empty($_POST[PASSWORD])) {
            include("{$_SERVER['DOCUMENT_ROOT']}/application/accounts/accounts_processor.php");

            $account_id = try_get_account_id($_POST[LOGIN], $_POST[PASSWORD]);
            if (isset($account_id) and $account_id != false) {
                $_SESSION[ACCOUNT_SESSION_NAME] = $account_id;
                header("Location: index.php");
                return;
            }
        }
    }
    $invalid_login = true;
} else {
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login </title>

    <link type="text/css" rel="stylesheet" href="content/css/materialize.min.css" media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="content/js/plugins/materialize.min.js"></script>
    <link rel="stylesheet" type="text/css" href="content/css/create_order.css">
</head>
<body>
<main>
    <div class="container">
        <div class="col m12">
            <form class="col s12" method="POST" action="login.php">
                <h1 class="header center teal-text text-lighten-2">Login</h1>
                <div class="row">
                    <div class="row">
                        <div class="input-field col s5">
                            <input name="login" id="login" type="text"
                                   class="validate <?php echo isset($invalid_login) ? "invalid" : "" ?>"
                                value="<?php echo isset($invalid_login) ? $_POST[LOGIN] : "" ?>">

                            <label for="login">Login</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s5">
                            <input name="password" id="pass" type="password"
                                   class="validate <?php echo isset($invalid_login) ? "invalid" : "" ?>"
                                   value="<?php echo isset($invalid_login) ? $_POST[PASSWORD] : ""  ?>">
                            <label for="pass">Password</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s5">
                            <div class="divider"></div>
                            <p class="right-align">
                                <button class="btn btn-large waves-effect waves-light" type="submit" name="action">Login
                                </button>
                            </p>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</main>
<footer class="page-footer teal lighten-2">
    <div class="footer-copyright">
        <div class="container">
            © 2014 Copyright by me
            <a class="grey-text text-lighten-4 right" href="/">Main</a>
        </div>
    </div>
</footer>


<script src="content/js/utils.js"></script>
<!--/<script src="content/js/create_order.js"></script>-->
</body>
</html>

