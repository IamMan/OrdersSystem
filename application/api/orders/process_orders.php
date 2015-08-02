<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.15
 * Time: 22:04
 */

define('CURSOR_KEY', 'cursor');
define('CURSOR_NAN', 'Cursot not a number');
define('SIZE_KEY', 'size');
define('SIZE_NAN', 'Size not a number');
define('SIZE_TOO_BIG', 'Size too big');
define('MAX_SIZE', '1000');
define('DEFAULT_SIZE', '100');


function validate_get() {
    $cursor = null;
    $size = null;

    if(isset($_GET[CURSOR_KEY])) {
        $cursor = $_GET[CURSOR_KEY] + 0;
        if (!is_int($cursor)) {
            return error_result(CURSOR_NAN);
        }
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

    return success_result(array(CURSOR_KEY => $cursor, SIZE_KEY => $size));

}

function process_orders_from_get() {
    $validate_object = validate_get();
    if (is_success($validate_object)) {
        $cursor = $validate_object[INFO_FIELD_NAME][CURSOR_KEY];
        $size = $validate_object[INFO_FIELD_NAME][SIZE_KEY];
        $orders_result = select_orders($cursor, $size);
        return $orders_result;
    } else {
        return $validate_object;
    }
}

define('SELECT_TOP_ORDERS',  "SELECT  * FROM (SELECT id FROM orders WHERE resolver_id IS NULL ORDER BY id DESC LIMIT ?) o JOIN orders l ON l.id = o.id ORDER BY l.id DESC ");
define('SELECT_FROM_ORDERS', "SELECT  * FROM (SELECT id FROM orders WHERE resolver_id IS NULL ORDER BY id DESC LIMIT ?, ?) o JOIN orders r ON l.id = o.id ORDER BY l.id DESC ");
function select_orders($from, $size) {

    include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");

    try {
        $query = null;
        $args = null;
        if ($from != null) {
            $query = SELECT_FROM_ORDERS;
            $args = array($from, $size);
        } else {
            $query = SELECT_TOP_ORDERS;
            $args = array($size + 0);
        }
        $connection = get_connect_to_orders();
        $result = execute_query($connection, $query, $args);

        if(is_last_query_success($connection)) {
            return success_result($result->fetchAll());
        } else {
            return error_result('Db error');
        }

    } catch(Exception $ex) {
        return error_result('Db error');
    }
}