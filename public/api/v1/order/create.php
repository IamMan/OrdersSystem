<?php
require '../../../database.php';
require '../../../../private/globalconstants.php';
require '../../../../private/order_stuff.php';

function validate_title($title)
{
    $titleError = null;
    if (empty($title)) {
        $titleError = 'Please enter title';
    } else if (strlen($title) > 50) {
        $titleError = 'Title should be less then 50';
    }
    return $titleError;
}

function validate_description($description)
{
    $descriptionError = null;
    if (empty($description)) {
        $descriptionError = 'Please enter description';
    } else if (strlen($description) > 2000) {
        $descriptionError = 'Description too big';
    }
    return $descriptionError;
}

function validate_price($price)
{

    $priceError = null;
    if (empty($price)) {
        $priceError = 'Please enter price';
    } else if (is_float($price)) {
        $price = (float)$price;
        if ($price - ORDER_PRICE_EPS < 0) {
            $priceError = 'Price should be positive';
        }
    }
    return $priceError;
}

function validate_field_from_order($order_object, $field_name, $validate_function)
{
    if (isset($order_object, $field_name)) {
        return $validate_function($order_object[$field_name]);
    } else {
        return "$field_name is not set";
    }
}

function validate_row_order()
{
    $rowError = null;
    if (!isset($_POST[ORDER_OBJECT])) {
        $rowError = 'Order is null';
    }
    return $rowError;
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


function validate_order()
{
    $rowError = validate_row_order();
    if ($rowError != null) {
        return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => array('row_' . ERROR_RESULT => $rowError));
    }

    $order_object = $_POST[ORDER_OBJECT];
//    if ($rowError != null) {
//        return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => array('row_' . ERROR_RESULT => 'Cannot parse'));
//    }

    $titleError = validate_field_from_order($order_object, TITLE_FIELD_NAME, TITLE_VALIDATION_FUNCTION_NAME);
    $descriptionError = validate_field_from_order($order_object, DESCRIPTION_FIELD_NAME, DESCRIPTION_VALIDATION_FUNCTION_NAME);
    $priceError = validate_field_from_order($order_object, PRICE_FIELD_NAME, PRICE_VALIDATION_FUNCTION_NAME);

    if ($titleError == null and $descriptionError == null and $priceError == null) {
        return array(RESULT_FIELD_NAME => SUCCESS_RESULT, ORDER_OBJECT => $order_object);
    }
    return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => array(
        TITLE_FIELD_NAME  => $titleError,
        DESCRIPTION_FIELD_NAME => $descriptionError,
        PRICE_FIELD_NAME => $priceError
        )
    );
}

header('Content-type: application/json');

if (!empty($_POST)) {

    $order_object = null;
    $validate_object = validate_order();
    if (array_key_exists(ERRORS_FIELD_NAME, $validate_object)) {
        echo json_encode($validate_object);
        return;
    } else {
        $order_object = $validate_object[ORDER_OBJECT];
    }

    if ($order_object != null) {
        include("../../../../private/order_stuff.php");
        $insert_object = insert_order($order_object);

        if (array_key_exists($insert_object, ERRORS_FIELD_NAME)) {
            $result = array(RESULT_FIELD_NAME => ERROR_RESULT);
            $result[ERRORS_FIELD_NAME] = $insert_object;
        } else {
            $result = array(RESULT_FIELD_NAME => SUCCESS_RESULT);
        }
        echo json_encode($result);

    } else {
        echo json_encode($validate_object);
    }
}