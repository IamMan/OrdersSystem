<?php

include('layout.php');
session_start();
session_regenerate_id();
if(!isset($_SESSION[ACCOUNT_SESSION_NAME]))      // if there is no valid session
{
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="content/css/materialize.min.css" media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="content/js/plugins/materialize.min.js"></script>
    <link rel="stylesheet" type="text/css" href="content/css/main.css">
</head>

<body>

<header>
    <div class="section no-pad-bot teal lighten-2" id="index-banner">
        <div class="container">
            <h1 class="header center-on-small-only">Orders system</h1>
            <div class="row center">
                <h4 class="header col s12 light center">You can create and resolve orders for money</h4>
            </div>
            <div class="row center">
                <a href="create.php" id="download-button" class="btn-large waves-effect waves-light">Create Order</a>
            </div>
            <div class="row center"><a class="white-text text-lighten-4">alpha prerelease v0.97.0</a></div>

            <br>
        </div>
        <div class="update-last teal lighten-3">
            <div class="container">
                <row>
                    <div class="col s5">
                    <div class="updates ">
                        Latest Update at <span id="last_update"></span>
                        <span id="neworders"></span>
                        <a id="load-new-button" class="btn-flat right teal-text-lighten-5 waves-effect waves-light hide-on-small-only">Load New</a>
                    </div>
                    </div>
                </row>
            </div>
        </div>
    </div>

</header>

<main>
<div class="container">
    <div class="row">
        <div class="col s10 offset-s1">
            <div class="collection orders">

            </div>
        </div>
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
<script src="content/js/main.js"></script>
</body>
</html>