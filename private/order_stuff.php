<?php

define('ORDER_OBJECT', 'order');

define('TITLE_FIELD_NAME', 'title');
define('DESCRIPTION_FIELD_NAME', 'description');
define('PRICE_FIELD_NAME', 'price');
define('CREATOR_ID_FIELD_NAME', 'creator_id');

define('TITLE_VALIDATION_FUNCTION_NAME', 'validate_title');
define('DESCRIPTION_VALIDATION_FUNCTION_NAME', 'validate_description');
define('PRICE_VALIDATION_FUNCTION_NAME', 'validate_price');

define("ORDER_TITLE_MAX_LENGTH", 50);
define("ORDER_DESCRIPTION_MAX_LENGTH", 2000);
define("ORDER_PRICE_MAX_LENGTH", 15);
define("ORDER_CREATOR_ID_MAX_LENGTH", 10);

define("ORDER_MAX_SIZE", ORDER_TITLE_MAX_LENGTH
                        + ORDER_DESCRIPTION_MAX_LENGTH
                        + ORDER_PRICE_MAX_LENGTH
                        + ORDER_CREATOR_ID_MAX_LENGTH
                        + 50);

define('ORDER_PRICE_EPS', 0.001);


function insert_order($order_object) {

    return array();
    $pdo = connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO orders (title,text,cost) values(?, ?, ?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($title, $description, $price));

}
