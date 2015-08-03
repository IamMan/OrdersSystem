<?php

include('layout.php');
session_start();
session_regenerate_id();
if(!isset($_SESSION[ACCOUNT_SESSION_NAME]))      // if there is no valid session
{
    header("Location: login.php");
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="content/css/materialize.min.css" media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="content/js/plugins/materialize.min.js"></script>
    <link rel="stylesheet" type="text/css" href="content/css/create_order.css">
</head>

<body>

<header>
    <div class="container">
        <div class="updates">
            Latest Update at <span class="synctime"></span>:
            <span class="date">4 days ago</span>
            <a id="load-new-button" class="btn-flat right grey-text text-lighten-2 waves-effect waves-light hide-on-small-only">Load New</a>
        </div>
    </div>
</header>

<main>
    <ul class="collection orders">

    </ul>
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