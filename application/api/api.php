<?php

define('RESULT_FIELD_NAME', 'result');
define('ERRORS_FIELD_NAME', 'errors');
define('INFO_FIELD_NAME', 'info');

define('ERROR_RESULT', 'error');
define('SUCCESS_RESULT', 'ok');

ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );

define('ACCOUNT_SESSION_NAME', 'at');

function error_result($object) {
    return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => $object);
}

function success_result($object) {
    return array(RESULT_FIELD_NAME => SUCCESS_RESULT, INFO_FIELD_NAME => $object);
}

function is_success($result) {
    return $result[RESULT_FIELD_NAME] == SUCCESS_RESULT;
}

function create_order_from_post() {
    include('order/process_order.php');
    return create_order_method_post();
}

function resolve_order_from_get() {
    include('order/process_order.php');
    return resolve_order_method_get();
}

function get_orders_list_from_get() {
    include('orders/process_orders.php');
    return get_orders_list_method_get();
}

function get_last_order_id()
{
    include('orders/process_orders.php');
    return process_last_order_id_from_get();
}