<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.15
 * Time: 22:04
 */

include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");

define('FROM_KEY', 'from');
define('TO_KEY', 'to');
define('CURSOR_NAN', 'Cursor not a number');
define('CONFLICT_ARGS', 'Conflict args');
define('SIZE_KEY', 'size');
define('SIZE_NAN', 'Size not a number');
define('SIZE_TOO_BIG', 'Size too big');
define('MAX_SIZE', '1000');
define('DEFAULT_SIZE', '100');

function validate_from() {


}

function validate_get_list() {
    $from = null;
    $to = null;
    $size = null;

    if(isset($_GET[FROM_KEY])) {
        $from = $_GET[FROM_KEY] + 0;
        if (!is_int($from)) {
            return error_result(CURSOR_NAN);
        }
    }

    if(isset($_GET[TO_KEY])) {
        $to = $_GET[TO_KEY] + 0;
        if (!is_int($to)) {
            return error_result(CURSOR_NAN);
        }
    }

    if (isset($from) and isset($to)) {
        return error_result(CONFLICT_ARGS);
    }

    if(isset($_GET[SIZE_KEY])) {
        $size = $_GET[SIZE_KEY] + 0;
        if (!is_int($size)) {
            return error_result(CURSOR_NAN);
        }
        if ($size > MAX_SIZE) {
            return error_result(SIZE_TOO_BIG);
        }
    } else {
        $size = DEFAULT_SIZE;
    }

    if (isset($from)) {
        return success_result(array(SIZE_KEY => $size, FROM_KEY => $from));
    } else {
        return success_result(array(SIZE_KEY => $size, TO_KEY => $to));
    }

}

function get_orders_list_method_get() {
    $validate_object = validate_get_list();
    if (is_success($validate_object)) {
        $orders_result = null;
        if (array_key_exists(FROM_KEY, $validate_object[INFO_FIELD_NAME])) {
            $cursor = $validate_object[INFO_FIELD_NAME][FROM_KEY];
            $size = $validate_object[INFO_FIELD_NAME][SIZE_KEY];
            $orders_result = select_orders_from($cursor, $size);
        } else {
            $cursor = null;
            if (isset($validate_object[INFO_FIELD_NAME][TO_KEY])) {
                $cursor = $validate_object[INFO_FIELD_NAME][TO_KEY];
            }
            $size = $validate_object[INFO_FIELD_NAME][SIZE_KEY];
            $orders_result = select_orders_to($cursor, $size);
        }

        return $orders_result;
    } else {
        return $validate_object;
    }
}

define('SELECT_ORDERS_TO',   "SELECT  * FROM (SELECT id FROM orders WHERE id < ? AND resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders l ON l.id = o.id ORDER BY l.id DESC ");
define('SELECT_TOP_ORDERS',   "SELECT  * FROM (SELECT id FROM orders WHERE resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders l ON l.id = o.id ORDER BY l.id DESC ");
define('SELECT_ORDERS_FROM', "SELECT  * FROM (SELECT id FROM orders WHERE id > ? AND resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders r ON l.id = o.id ORDER BY l.id DESC ");
function select_orders_from($from, $size) {
    return select_orders(SELECT_ORDERS_FROM, $from, $size);
}

function select_orders_to($to, $size) {
    if (isset($to)) {
        return select_orders(SELECT_ORDERS_TO, $to, $size);
    } else {
        return select_orders(SELECT_TOP_ORDERS, null, $size);
    }

}

function select_orders($query, $cursor, $size) {
    try {
        $args = null;
        if (isset($cursor)) {
            $args = array($cursor + 0, $size + 0);
        } else {
            $args = array($size + 0);
        }
        $connection = get_connect_to_orders();
        $result = execute_query($connection, $query, $args);

        if(is_last_query_success($connection)) {
            return success_result($result->fetchAll(PDO::FETCH_ASSOC));
        } else {
            return error_result('db error');
        }

    } catch(Exception $ex) {
        return error_result('db error');
    }
}

function process_last_order_id_from_get() {
    $select_last_result = select_last_id();
    return $select_last_result;
}

define('SELECT_MAX_ORDER_ID',  "SELECT MAX(id) FROM orders");
function select_last_id() {
    try {
        $query = SELECT_MAX_ORDER_ID;
        $connection = get_connect_to_orders();
        $result = execute_query($connection, $query, array(null));
        if(is_last_query_success($connection)) {
            return success_result($result->fetchAll()[0][0]);
        } else {
            return error_result(ERROR_MESSAGE);
        }

    } catch(Exception $ex) {
        return error_result(ERROR_MESSAGE);
    }
}