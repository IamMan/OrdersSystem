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
<nav class="white" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="#" class="brand-logo">Logo</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="#">Navbar Link</a></li>
        </ul>

        <ul id="nav-mobile" class="side-nav">
            <li><a href="#">Navbar Link</a></li>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>
<div id="index-banner" class="parallax-container">
    <div class="section no-pad-bot">
        <div class="container">
            <h1 class="header center teal-text text-lighten-2">Create Order</h1>
            <br><br>

        </div>
    </div>
    <div class="parallax"><img src="/content/img/background1.jpg" alt="Unsplashed background img 1"></div>
</div>
</header>
<!--b-header-->
<main>
<div class="container">
    <div class="section">
        <div class="row">
            <form class="col s8 offset-s3" id="order_form">
                <div class="row">
                    <div class="input-field col s4">
                        <input name="title" type="text" class="validate" length="50">
                        <label for="title" data-error="wrong" data-success="right">Title</label>
                    </div>
                    <div class="input-field col s4">
                        <input name="price" type="text" class="validate" length="20">
                        <label for="price" data-error="wrong" data-success="right">Price</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s8">
                        <textarea name="description" class="materialize-textarea" length="2000"></textarea>
                        <label for="description" data-error="wrong">Description</label>
                    </div>
                </div>
                <div class="row">
                    <button class="btn waves-effect waves-light submit">Submit
                        <i class="mdi-content-send right"></i>
                    </button>
                    <a class="waves-effect waves-teal btn-flat clear">Clear</a>
                </div>
            </form>
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