<?php

include('cash.php');

define('ORDER_ID_FIELD_NAME', 'id');
define('TITLE_FIELD_NAME', 'title');
define('DESCRIPTION_FIELD_NAME', 'description');
define('PRICE_FIELD_NAME', 'price');
define('CREATOR_ID_FIELD_NAME', 'creator_id');
define('RESOLVER_ID_FIELD_NAME', 'resolver_id');

define('RESULT_FIELD_NAME', 'result');
define('ERRORS_FIELD_NAME', 'errors');
define('INFO_FIELD_NAME', 'info');

define('ERROR_RESULT', 'error');
define('SUCCESS_RESULT', 'ok');
define('CASHED_RESULT', 'cash');

ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );

define('ACCOUNT_SESSION_NAME', 'at');

function error_result($object) {
    return array(RESULT_FIELD_NAME => ERROR_RESULT, ERRORS_FIELD_NAME => $object);
}

function success_result($object) {
    return array(RESULT_FIELD_NAME => SUCCESS_RESULT, INFO_FIELD_NAME => $object);
}

function json_cashed_result($object) {
    $cash_json = null;
    if (is_array($object)) {
        $cash_json = "[".implode(",",$object)."]";
    } else {
        $cash_json = $object;
    }

    $info_string = str_replace("null", $cash_json,json_encode(success_result(null)));
    return array(RESULT_FIELD_NAME => CASHED_RESULT, INFO_FIELD_NAME => $info_string);
}

function is_success($result) {
    return $result[RESULT_FIELD_NAME] == SUCCESS_RESULT;
}

function is_cashed($result) {
    return $result[RESULT_FIELD_NAME] == CASHED_RESULT;
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
function get_orders_deleted_from_get() {
    include('orders/process_orders.php');
    return get_orders_deleted_method_get();
}
