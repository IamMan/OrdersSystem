<?php

function validate_id($orderId) {
    $orderError  = null;
    if (empty($orderId)) {
        $priceError = 'OrderId enter orderid';
        return $priceError;
    }
    $orderId = $orderId+ 0;
    if (!is_integer($orderId)) {
        $priceError = 'OrderID is a number';
        return $priceError;
    }
    if ($orderId < 1) {
        $priceError = 'OrderId to small';
        return $priceError;
    }
}

function validate_title($title)
{
    $titleError = null;
    if (empty($title)) {
        $titleError = 'Please enter title';
        return $titleError;
    }
    if (strlen($title) > 50) {
        $titleError = 'Title should be less then 50';
        return $titleError;
    }
    return $titleError;
}

function validate_description($description)
{
    $descriptionError = null;
    if (empty($description)) {
        $descriptionError = 'Please enter description';
        return $descriptionError;
    }
    if (strlen($description) > 2000) {
        $descriptionError = 'Description too big';
        return $descriptionError;
    }
    return $descriptionError;
}

function validate_price($price)
{

    $priceError = null;
    if (empty($price)) {
        $priceError = 'Please enter price';
        return $priceError;
    }
//    if (!is_float($price)) {
//        $priceError = 'Price is a number';
//        return $priceError;
//    }
    $price = $price + 0;
    if (!is_integer($price)) {
        $priceError = 'Price is a number';
        return $priceError;
    }
    if ($price - ORDER_PRICE_EPS < 0) {
        $priceError = 'Price should be positive';
        return $priceError;
    }
    return $priceError;
}

function validate_field_from_array($order_object, $field_name, $validate_function)
{
    if (isset($order_object) and isset($order_object[$field_name])) {
        return $validate_function($order_object[$field_name]);
    } else {
        return "$field_name is not set";
    }
}

function validate_post()
{
    $rowError = null;
    if (!isset($_POST[ORDER_OBJECT])) {
        $rowError = 'Order is null';
    }
    if ($rowError != null) {
        return error_result($rowError);
    }
    $orderObject = $_POST[ORDER_OBJECT];
    return success_result($orderObject);
}

function try_decode_order_object($json_string)
{
    $order_object = json_decode($json_string, true, 2);
    if (json_last_error() == JSON_ERROR_NONE) {
        return $order_object;
    } else {
        return null;
    }
}

function validate_order($order_object)
{
    $titleError = validate_field_from_array($order_object, TITLE_FIELD_NAME, TITLE_VALIDATION_FUNCTION_NAME);
    $descriptionError = validate_field_from_array($order_object, DESCRIPTION_FIELD_NAME, DESCRIPTION_VALIDATION_FUNCTION_NAME);
    $priceError = validate_field_from_array($order_object, PRICE_FIELD_NAME, PRICE_VALIDATION_FUNCTION_NAME);

    if ($titleError == null and $descriptionError == null and $priceError == null) {
        return success_result(null);
    }
    return error_result(array(
        TITLE_FIELD_NAME => $titleError,
        DESCRIPTION_FIELD_NAME => $descriptionError,
        PRICE_FIELD_NAME => $priceError
    )
    );
}