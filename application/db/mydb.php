<?php

include('db_configs.php');

function get_connect_to_orders() {
    $connection = new PDO(ORDERS_CONNECTION, ORDERS_USER_NAME, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

function get_connect_to_accounts() {
    $connection = new PDO(ACCOUNTS_CONNECTION, ORDERS_USER_NAME, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

function get_connect_to_transaction_log() {
    $connection = new PDO(TRANSACTIONS_CONNECTION, ORDERS_USER_NAME, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

function get_connect_to_xa() {
    $connection = new PDO(TRANSACTIONS_CONNECTION, ORDERS_USER_NAME, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

function execute_query($connection, $query, $args) {
    try {
        $q = $connection->prepare($query);
        $q = binds($q,  $args);
        $q->execute();
        return $q;
    } catch(Exception $ex) {
        error_log($ex->getMessage());
        throw $ex;
    }
}

function binds($query, $args) {
    $now = 1;
    foreach ($args as $key => $value) {
        if (is_int($value)) {
            $query->bindValue($key + 1, $value, PDO::PARAM_INT);
        } else {
            $query->bindValue($key + 1, $value);
        }
    }
    return $query;
}

function is_last_query_success ($connection) {
    if($connection->errorInfo()[0] == '00000') {
        return true;
    }
        else {
            return false;
        }

}

function two_phase_commit($xid, $logic, $logic_params) {

    try {
        $xa_connection = get_connect_to_xa();

        $is_success = open_xa_transaction($xa_connection, $xid);
        if (!$is_success)  {
            return false;
        }

        try {
            $is_logic_success = $logic($logic_params);
        }
        catch (Exception $ex) {
            $is_logic_success = false;
        }

        $is_success = end_xa_transaction($xa_connection, $xid);
        $is_success = prepare_xa_transaction($xa_connection, $xid);

        if($is_logic_success) {
            $is_success = commit_xa_transaction($xa_connection, $xid);
            return $is_success;
        } else {
            rollback_xa_transaction($xa_connection, $xid);
            return false;
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        return false;
    }

}

define('OPEN_XA_TRANSACTION', 'XA START ?');
function open_xa_transaction($xa_connection, $xid) {
    $args = array($xid);
    execute_query($xa_connection, OPEN_XA_TRANSACTION, $args);
    return is_last_query_success($xa_connection);
}

define('END_XA_TRANSACTION', 'XA END ?');
function end_xa_transaction($xa_connection, $xid) {
    $args = array($xid);
    execute_query($xa_connection, END_XA_TRANSACTION, $args);
    return is_last_query_success($xa_connection);
}

define('PREPARE_XA_TRANSACTION', 'XA PREPARE ?');
function prepare_xa_transaction($xa_connection, $xid) {
    $args = array($xid);
    execute_query($xa_connection, PREPARE_XA_TRANSACTION, $args);
    return is_last_query_success($xa_connection);
}

define('COMMIT_XA_TRANSACTION', 'XA COMMIT ?');
function commit_xa_transaction($xa_connection, $xid) {
    $args = array($xid);
    execute_query($xa_connection, COMMIT_XA_TRANSACTION, $args);
    return is_last_query_success($xa_connection);
}

define('ROLLBACK_XA_TRANSACTION', 'XA ROLLBACK ?');
function rollback_xa_transaction($xa_connection, $xid) {
    $args = array($xid);
    execute_query($xa_connection, ROLLBACK_XA_TRANSACTION, $args);
    return is_last_query_success($xa_connection);
}