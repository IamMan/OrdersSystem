<?php
require '../../../database.php';

$EPS = 0.0001;

function validate_title($title) {
    $titleError = null;
    if (empty($title)) {
        $titleError = 'Please enter Name';
    } else if (strlen($title) > 50) {
        $titleError = 'Title should be less then 50';
    }
    return $titleError;
}

function validate_description($description) {
    $descriptionError = null;
    if (empty($text)) {
        $descriptionError = 'Please enter description';
    } else if (strlen($description) > 2000) {
        $descriptionError = 'Description too big';
    }
    return $descriptionError;
}

function validate_price($price) {
    global $EPS;
    $priceError = null;
    if (empty($price)) {
        $priceError = 'Please enter price';
    } else if (is_float($price)) {
        $price = (float)$price;
        if ($price - $EPS < 0) {
            $priceError = 'Price should be positive';
        }
    }
    return $priceError;
}

header('Content-type: application/json');

if (!empty($_POST)) {
// keep track post values
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

// keep track validation errors
    $titleError = validate_title($title);
    $descriptionError = validate_description($description);
    $priceError = validate_price($price);
 // validate input
    $valid = strlen($titleError.$descriptionError.$priceError) > 0;

// insert data
    if ($valid) {
        $pdo = connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO orders (title,text,cost) values(?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute(array($title, $description, $price));

        echo json_encode(array('result' => 'ok'));
    } else {
        echo json_encode(array(
                'result' => 'error',
                'titleError' => $titleError,
                'descriptionError' => $descriptionError,
                'priceError' => $priceError)
        );
    }
}
?>

