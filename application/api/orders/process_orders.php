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
    $from = null;
    if(isset($_GET[FROM_KEY])) {
        $from = $_GET[FROM_KEY] + 0;
        if (!is_int($from)) {
            return error_result(CURSOR_NAN);
        }
    }
    return success_result($from);
}

function validate_to() {
    $to = null;
    if(isset($_GET[TO_KEY])) {
        $to = $_GET[TO_KEY] + 0;
        if (!is_int($to)) {
            return error_result(CURSOR_NAN);
        }
    }
    return success_result($to);
}

function validate_get_list() {
    $from = null;
    $to = null;
    $size = null;

    $validate_from_result = validate_from();
    if (is_success($validate_from_result)) {
        $from = $validate_from_result[INFO_FIELD_NAME];
    }

    $validate_to_result = validate_to();
    if (is_success($validate_to_result)) {
        $to = $validate_to_result[INFO_FIELD_NAME];
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

    return success_result(array(SIZE_KEY => $size + 0, FROM_KEY => $from, TO_KEY => $to));
}

function get_orders_list_method_get() {
    $validate_object = validate_get_list();
    if (is_success($validate_object)) {
        $orders_result = null;

        if (isset($validate_object[INFO_FIELD_NAME][TO_KEY]) and isset($validate_object[INFO_FIELD_NAME][FROM_KEY])) {
            return error_result(CONFLICT_ARGS);
        }

        if (isset($validate_object[INFO_FIELD_NAME][FROM_KEY])) {
            $cursor = $validate_object[INFO_FIELD_NAME][FROM_KEY];
            $size = $validate_object[INFO_FIELD_NAME][SIZE_KEY];
            $orders_result = select_orders_from($cursor, $size);
            return $orders_result;
        } else {
            $cursor = null;
            if (isset($validate_object[INFO_FIELD_NAME][TO_KEY])) {
                $cursor = $validate_object[INFO_FIELD_NAME][TO_KEY];
            }
            $size = $validate_object[INFO_FIELD_NAME][SIZE_KEY];
            $orders_result = select_orders_to($cursor, $size);
            return $orders_result;
        }
        return $orders_result;
    } else {
        return $validate_object;
    }
}

define('SELECT_ORDERS_TO',    "SELECT o.id, title, description, price FROM (SELECT id FROM orders WHERE id <= ? AND resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders l ON l.id = o.id ORDER BY l.id DESC ");
define('SELECT_TOP_ORDERS',   "SELECT o.id, title, description, price FROM (SELECT id FROM orders WHERE resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders l ON l.id = o.id ORDER BY l.id DESC ");
define('SELECT_ORDERS_FROM',  "SELECT o.id, title, description, price FROM (SELECT id FROM orders WHERE id >= ? AND resolver_id IS NULL ORDER BY id ASC LIMIT ?) o JOIN orders r ON r.id = o.id ORDER BY r.id DESC ");
function select_orders_from($from, $size) {
    $cashed = cash_get_opened_from($from, $size);
    if ($cashed != false) {
        if (count($cashed) > 0) {
            return json_cashed_result($cashed);
        }
    }
    return select_orders(SELECT_ORDERS_FROM, $from, $size);

}

function select_orders_to($to, $size) {
    if (isset($to)) {
        $cashed = cash_get_opened_to($to-1, $size);

        if ($cashed != false) {
            if (count($cashed) > 0) {
                return json_cashed_result($cashed);
            }
        }

        return select_orders(SELECT_ORDERS_TO, $to, $size);
    } else {

        $max_id_res = select_last_id();
        $max_id_cash = cash_max_id();
        if ($max_id_cash != false) {
            $max_id_cash = array_values($max_id_cash)[0];
        } else {
            $max_id_cash = -1;
        }
        if (is_success($max_id_res) and $max_id_res[INFO_FIELD_NAME] == $max_id_cash) {
            $cashed = cash_get_opened_top($size);

            if ($cashed != false) {

                if (count($cashed) > 0) {
                    return json_cashed_result($cashed);
                }
            }

        }
        return select_orders(SELECT_TOP_ORDERS, null, $size);
    }

}

function select_orders($query, $cursor, $size) {
    try {
        $args = null;
        if (isset($cursor)) {
            $args = array($cursor, $size);
        } else {
            $args = array($size);
        }
        $connection = get_connect_to_orders();
        $result = execute_query($connection, $query, $args);

        if(is_last_query_success($connection)) {
            $orders =  $result->fetchAll(PDO::FETCH_ASSOC);
            cash_update_opened($orders);
            return success_result($orders);
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

define('SELECT_MAX_ORDER_ID',  "SELECT MAX(id) FROM orders WHERE resolver_id IS NULL");
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

function get_orders_deleted_method_get() {
    return error_result(DEPRECATED);

    $validate_object = validate_get_list();
    if (is_success($validate_object)) {
        $orders_result = null;

        if (!isset($validate_object[INFO_FIELD_NAME][TO_KEY])) {
            return error_result(TO_KEY);
        }

        if (!isset($validate_object[INFO_FIELD_NAME][FROM_KEY])) {
            return error_result(FROM_KEY);
        }

        $from = $validate_object[INFO_FIELD_NAME][FROM_KEY] + 0;
        $to = $validate_object[INFO_FIELD_NAME][TO_KEY] + 0;
        if ($to - $from <= 0 and $to - $from > MAX_SIZE) {
            return error_result(SIZE);
        }

        $deleted_orders_result = select_deleted_orders_from_to($from, $to);

        return $deleted_orders_result;
    } else {
        return $validate_object;
    }
}

define('SELECT_DELETE_ORDERS_FROM_TO', "SELECT id FROM orders WHERE ? <= id and id <= ? AND resolver_id IS NOT NULL");
function select_deleted_orders_from_to($from, $to) {
    try {
        $connection = get_connect_to_orders();
        $args = array($from + 0, $to + 0);
        $result = execute_query($connection, SELECT_DELETE_ORDERS_FROM_TO, $args);
        if(is_last_query_success($connection)) {
            return success_result($result->fetchAll(PDO::FETCH_NUM));
        } else {
            return error_result(ERROR_MESSAGE);
        }

    } catch(Exception $ex) {
        return error_result(ERROR_MESSAGE);
    }

}