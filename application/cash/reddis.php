<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.08.15
 * Time: 15:06
 */

define('REDIS_SERVER_HOST', 'localhost');
define('REDIS_SERVER_PORT', 6379);

define('OPEN_ORDERS_ORDERED_SET', 'opens');
define('OPEN_ORDERS_ORDERED_SET_DB', 1);
define('RESOLVED_ORDERS_ORDERED_SET', 'resolved');
define('RESOLVED_ORDERS_ORDERED_SET_DB', 1);

function connection_to_active_orders_cash() {
    $redis = new Redis();
    try {
        $redis->pconnect(REDIS_SERVER_HOST, REDIS_SERVER_PORT, 3.4);
        if ($redis->select(OPEN_ORDERS_ORDERED_SET_DB)) {
            return $redis;
        } else {
            return false;
        }
    }
    catch (Exception $ex)
    {
        error_log($ex->getMessage());
    }
    return false;
}

function connection_to_resolved_orders_cash() {
    $redis = new Redis();
    try {
        $redis->pconnect(REDIS_SERVER_HOST, REDIS_SERVER_PORT, 3.4);
        if ($redis->select(RESOLVED_ORDERS_ORDERED_SET_DB)) {
            return $redis;
        } else {
            return false;
        }
    } catch (Exception $ex)
    {
        error_log($ex->getMessage());
    }
    return false;
}

