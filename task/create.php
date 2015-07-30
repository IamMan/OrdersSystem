<?php
require '../database.php';

if (!empty($_POST)) {
// keep track validation errors
    $titleError = null;
    $textError = null;
    $costError = null;

// keep track post values
    $title = $_POST['title'];
    $text = $_POST['text'];
    $cost = $_POST['cost'];

// validate input
    $valid = true;

    if (empty($title)) {
        $titleError = 'Please enter Name';
        $valid = false;
    } else if ( strlen($title) > 50) {
        $titleError = 'Title should be less then 50';
        $valid = false;
    }

    if (empty($text)) {
        $textError = 'Please enter description';
        $valid = false;
    } else if ( strlen($title) > 2000) {
        $textError = 'Description too big';
        $valid = false;
    }

    if (empty($cost)) {
        $mobileError = 'Please enter price';
        $valid = false;
    } else if (is_float($cost)) {
        $mobileError = 'Price should be a number';
        $valid = false;
    }

// insert data
    if ($valid) {
        $pdo = connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO orders (title,text,cost) values(?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute(array($title, $text, $cost));
        header("Location: index.php");
    }
}
?>