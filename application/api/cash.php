<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.08.15
 * Time: 15:36
 */

include("{$_SERVER['DOCUMENT_ROOT']}/application/cash/reddis.php");

define('OPENED_ORDER_PREFIX', 'openorder::');
define('RESOLVED_ORDER_PREFIX', 'closedorder::');

function cash_new_order_as_json($id, $order_object) {
    return false;
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    $open_orders_cash_connection->zAdd(OPEN_ORDERS_ORDERED_SET, $id, json_encode($order_object));
}

function cash_is_order_resolved($id) {
    $resolved_orders_cash_connection = connection_to_resolved_orders_cash();
        if ($resolved_orders_cash_connection == false) {
        return false;
    }
    return $resolved_orders_cash_connection->sContains(RESOLVED_ORDERS_ORDERED_SET, RESOLVED_ORDER_PREFIX.$id);
}

function cash_resolved_order($id) {
    $resolved_orders_cash_connection = connection_to_resolved_orders_cash();
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($resolved_orders_cash_connection == false || $open_orders_cash_connection == false) {
        return false;
    }
    $open_orders_cash_connection -> zRemRangeByScore(OPEN_ORDERS_ORDERED_SET, $id, $id);
    return $resolved_orders_cash_connection->sAdd(RESOLVED_ORDERS_ORDERED_SET, RESOLVED_ORDER_PREFIX.$id);
}

function cash_get_opened_top($size) {
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    return $open_orders_cash_connection->zRange(OPEN_ORDERS_ORDERED_SET, -$size, -1);
}

function cash_get_opened_to($to, $size) {
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    return $open_orders_cash_connection->zRange(OPEN_ORDERS_ORDERED_SET, max(0, $to-$size), $to);
}

function cash_get_opened_from($from, $size) {
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    return $open_orders_cash_connection->zRange(OPEN_ORDERS_ORDERED_SET, $from, $from + $size);
}

function cash_update_opened($opened) {
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    foreach($opened as $order) {
        $open_orders_cash_connection->zAdd(OPEN_ORDERS_ORDERED_SET, $order[ORDER_ID_FIELD_NAME] + 0, json_encode($order));
    }
    return true;
}

function cash_max_id() {
    $open_orders_cash_connection = connection_to_active_orders_cash();
    if ($open_orders_cash_connection == false) {
        return false;
    }
    return $open_orders_cash_connection->zRange(OPEN_ORDERS_ORDERED_SET, -1, -1, true);
}
