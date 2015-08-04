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
    <link rel="stylesheet" type="text/css" href="content/css/main.css">
</head>

<body>
<header>
<nav class="teal" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="#" class="brand-logo">Orders System</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="/public_html/index.php"">Orders</a></li>
        </ul>

        <a  href="/public_html/index.php" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">Orders</i></a>
    </div>
</nav>
<div id="index-banner" class="parallax-container">
    <div class="section no-pad-bot">
        <div class="container">
            <h1 class="header center teal-text text-lighten-2">Create Order</h1>
            <br><br>

        </div>
    </div>
    <div class="parallax"><img src="/public_html/content/img/background1.jpg" alt="Unsplashed background img 1"></div>
</div>
</header>
<!--b-header-->
<main>
<div class="container">
    <div class="section createorder">
        <div class="row">
            <form class="col s8 offset-s3" id="order_form">
                <row>
                <div class="row">
                    <div class="input-field col s4">
                        <input name="title" type="text" class="validate" length="50">
                        <label for="title">Title</label>
                    </div>
                    <div class="input-field col s4">
                        <input name="price" type="text" class="validate" length="20">
                        <label for="price">Price</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s8">
                        <textarea name="description" class="materialize-textarea" length="2000"></textarea>
                        <label for="description" >Description</label>
                    </div>
                </div>
                <div class="row s4">
<!--                    <div class="divider col s8"></div><br>-->
                    <button class="btn waves-effect waves-light submit">Submit
                        <i class="mdi-content-send right"></i>
                    </button>
                    <a class="waves-effect waves-teal btn-flat clear">Clear</a>
                </div>
                </row>
            </form>
        </div>
    </div>
    <div class="section created hide">
        <div class="row">
            <h2 class="header center teal-text text-lighten-2">Order Successfully Created</h2>
            <br>
            <div class="center">
                <button class="btn waves-effect waves-light center addorder">New Order</button>
                <a href="index.php"><button class="btn waves-effect waves-light center">Orders</button>
            </div>
        </div>
    </div>
</div>
<!--b-layout-->
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
<script src="content/js/create_order.js"></script>
</body>
</html>