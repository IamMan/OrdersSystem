<?php

define('RESULT_FIELD_NAME', 'result');
define('ERRORS_FIELD_NAME', 'errors');
define('INFO_FIELD_NAME', 'info');

define('ERROR_RESULT', 'error');
define('SUCCESS_RESULT', 'ok');

ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );

function error_result($object) {
    return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => $object);
}

function success_result($object) {
    return array(RESULT_FIELD_NAME => SUCCESS_RESULT, INFO_FIELD_NAME => $object);
}

function is_success($result) {
    return $result[RESULT_FIELD_NAME] == SUCCESS_RESULT;
}

function process_order() {
    include('order/process_order.php');
    return create_order_from_post();
}

function process_orders() {
    include('orders/process_orders.php');
    return process_orders_from_get();
}