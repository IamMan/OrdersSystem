<?php

define('ORDER_OBJECT', 'order');

define('ORDER_ID_FIELD_NAME', 'id');
define('TITLE_FIELD_NAME', 'title');
define('DESCRIPTION_FIELD_NAME', 'description');
define('PRICE_FIELD_NAME', 'price');
define('CREATOR_ID_FIELD_NAME', 'creator_id');
define('RESOLVER_ID_FIELD_NAME', 'resolver_id');

define('ORDER_ID_VALIDATION_FUNCTION_NAME', 'validate_id');
define('TITLE_VALIDATION_FUNCTION_NAME', 'validate_title');
define('DESCRIPTION_VALIDATION_FUNCTION_NAME', 'validate_description');
define('PRICE_VALIDATION_FUNCTION_NAME', 'validate_price');

define("ORDER_TITLE_MAX_LENGTH", 50);
define("ORDER_DESCRIPTION_MAX_LENGTH", 2000);
define("ORDER_PRICE_MAX_LENGTH", 15);
define("ORDER_CREATOR_ID_MAX_LENGTH", 10);

define("ORDER_MAX_SIZE", ORDER_TITLE_MAX_LENGTH
    + ORDER_DESCRIPTION_MAX_LENGTH
    + ORDER_PRICE_MAX_LENGTH
    + ORDER_CREATOR_ID_MAX_LENGTH
    + 50);

define('ORDER_PRICE_EPS', 0.001);
define('RATE', 0.98);

define('ERROR_MESSAGE', 'some error');
define('BAD_ORDER', 'bad order');
define('BAD_ACCOUNT', 'bad account');


function create_order_method_post() {
    include('validate_order.php');

    $validate_results = validate_post();
    if ($validate_results[RESULT_FIELD_NAME] == ERROR_RESULT) {
        return $validate_results;
    }
    return create_order($validate_results[INFO_FIELD_NAME]);
}

function create_order($order_object) {
    $validate_results = validate_order($order_object);
    if ($validate_results[RESULT_FIELD_NAME] == SUCCESS_RESULT) {
        $creator_id_result = get_creator_id();
        if ($creator_id_result[RESULT_FIELD_NAME] == SUCCESS_RESULT) {
            $creator_id = $creator_id_result[INFO_FIELD_NAME];
            if ($creator_id != null) {
                $order_object[CREATOR_ID_FIELD_NAME] = $creator_id;
                return $insert_result_object = insert_order($order_object);
            }
        }else {
            return $creator_id_result;
        }
    } else {
        return $validate_results;
    }
}

define("INSERT_ORDER_SQL", "INSERT INTO orders (title, description, price, creator_id) values(?, ?, ?, ?)");
function insert_order($order_object)
{
    include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");
    try {
        $pdo_connection = get_connect_to_orders();
        $args = array(
            $order_object[TITLE_FIELD_NAME],
            $order_object[DESCRIPTION_FIELD_NAME],
            $order_object[PRICE_FIELD_NAME] + 0,
            $order_object[CREATOR_ID_FIELD_NAME]);
            execute_query($pdo_connection, INSERT_ORDER_SQL, $args);
        if(is_last_query_success($pdo_connection)) {
            return success_result(null);
        } else {
            return error_result('Db error');
        }

    } catch (Exception $e) {
        return error_result('Db error');
    }
}

function resolve_order_method_get() {
    include('validate_order.php');
    $validate_results = null;
    if (!empty($_GET)){
        $validate_results = validate_field_from_array($_GET, ORDER_ID_FIELD_NAME, ORDER_ID_VALIDATION_FUNCTION_NAME);
    }
    if ($validate_results != null) {
        return error_result($validate_results);
    }
    return resolve_order($_GET[ORDER_ID_FIELD_NAME]);
}

function resolve_order($order_id) {
    $resolver_result = get_resolver_id();
    $resolver_id = null;
    if (is_success($resolver_result)) {
        $resolver_id = $resolver_result[INFO_FIELD_NAME];
        return insert_resolve_order($order_id, $resolver_id);
    } else {
        return $resolver_result;
    }
}

define("SELECT_ORDER_ID_PRICE", "SELECT id, price, resolver_id FROM orders WHERE id = ?");
define("UPDATE_ACCOUNT_CASH", "UPDATE accounts SET cash = cash + ? WHERE id = ?");
define("UPDATE_ORDER_ID_RESOLVER", "UPDATE orders SET resolver_id = ? WHERE id = ?");
function insert_resolve_order($order_id, $resolver_id) {
    include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");

    try {
        $orders_connection = get_connect_to_orders();
        $accounts_connection = get_connect_to_accounts();

        $args = array($order_id + 0);
        $result = execute_query($orders_connection, SELECT_ORDER_ID_PRICE, $args);
        if(!is_last_query_success($orders_connection)) {
                return error_result(BAD_ORDER);
        }

        $order = $result-> fetchAll(PDO::FETCH_ASSOC);
        if (!isset($order) or count($order) != 1 or !isset($order[0]) or isset($order[0][RESOLVER_ID_FIELD_NAME])) {
            return error_result(BAD_ORDER);
        } else {
            $order = $order[0];
        }

        $price = $order[PRICE_FIELD_NAME];
        $cash = round($price * RATE, PHP_ROUND_HALF_DOWN);
        $orders_connection->beginTransaction();
        $accounts_connection->beginTransaction();

        $args = array($resolver_id, $order_id);
        execute_query($orders_connection, UPDATE_ORDER_ID_RESOLVER, $args);
        if (!is_last_query_success($orders_connection)) {
            $orders_connection->rollBack();
            $accounts_connection->rollBack();
            return error_result('BAD_ORDER_ID');
        }

        $args = array($cash, $resolver_id);
        $account_update_result = execute_query($accounts_connection, UPDATE_ACCOUNT_CASH, $args);
        if (!is_last_query_success($orders_connection) or $account_update_result->rowCount() != 1) {
            $orders_connection->rollBack();
            $accounts_connection->rollBack();
            return error_result(BAD_ACCOUNT);
        }

        $orders_connection->commit();
        $accounts_connection->commit();
        return success_result(null);
    } catch (Exception $e) {
        return error_result('error');
    }
}

function get_creator_id() {
    return get_account_id_form_session();
}

function get_resolver_id() {
    return get_account_id_form_session();
}

function get_account_id_form_session() {
    session_start();
    if(isset($_SESSION[ACCOUNT_SESSION_NAME]))
    {
        return success_result($_SESSION[ACCOUNT_SESSION_NAME]);
    }
    return error_result(BAD_ACCOUNT);
}