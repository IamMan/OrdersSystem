<?php

define('ORDER_OBJECT', 'order');

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
define('ORDER_RESOLVED', 'order already resolved');

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
                $insert_result_object = insert_order($order_object);
                return $insert_result_object;
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
            return error_result(ERROR_MESSAGE);
        }

    } catch (Exception $e) {
        return error_result(ERROR_MESSAGE);
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
    if (cash_is_order_resolved($order_id)) {
        return error_result(ORDER_RESOLVED);
    }
    $resolver_result = get_resolver_id();
    $resolver_id = null;
    if (is_success($resolver_result)) {
        $resolver_id = $resolver_result[INFO_FIELD_NAME];
        $insert_resolve_order_result = insert_resolve_order($order_id, $resolver_id);
        if (is_success($insert_resolve_order_result)) {
            cash_resolved_order($order_id);
        }
        return $insert_resolve_order_result;
    } else {
        return $resolver_result;
    }
}

define('RESOLVE_ORDER_LOGIC_FUNCTION_NAME', 'resolve_order_logic');
function insert_resolve_order($order_id, $resolver_id) {
    include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");

    try {
        $accounts_connection = get_connect_to_accounts();
        $is_account_exist = check_account_exist($accounts_connection, $resolver_id);
        if ($is_account_exist == false) {
            return error_result(BAD_ACCOUNT);
        }

        $orders_connection = get_connect_to_orders();
        $is_order_not_resolve = check_order_dont_resolve_and_return_order($orders_connection, $order_id);
        if ($is_order_not_resolve  == false) {
            return error_result(ORDER_RESOLVED);
        }

        $transactions_connection = get_connect_to_transaction_log();
        $xid = gen_xid_for_order_resolve($order_id, $resolver_id);

        $price = $is_order_not_resolve[0][PRICE_FIELD_NAME];
        $cash = calc_account_cash_add($price);

        $logic_params = array(
            ORDER_ID_FIELD_NAME => $order_id,
            RESOLVER_ID_FIELD_NAME => $resolver_id,
            CASH_LOGIC_PARAM => $cash,
            ORDERS_CONNECTION_LOGIC_PARAM => $orders_connection,
            ACCOUNTS_CONNECTION_LOGIC_PARAM => $accounts_connection,
            TRANSACTIONS_CONNECTION_LOGIC_PARAM => $transactions_connection,
        );

        if (two_phase_commit($xid, RESOLVE_ORDER_LOGIC_FUNCTION_NAME, $logic_params) == false) {
            return error_result(ERROR_MESSAGE);
        }

        return success_result(array(ORDER_ID_FIELD_NAME => $order_id, PRICE_FIELD_NAME => $cash));
    } catch (Exception $e) {
        return error_result(ERROR_MESSAGE);
    }
}

function gen_xid_for_order_resolve($order_id, $resolver_id) {
    return 'resolve_'.$order_id.$resolver_id;
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

function calc_account_cash_add($price) {
    return round($price * RATE, PHP_ROUND_HALF_DOWN);
}

define("CHECK_ACCOUNT_EXISTS", "SELECT 1 FROM accounts WHERE id = ?");
function check_account_exist($accounts_connection, $resolver_id) {
    $args = array($resolver_id + 0);
    $result = execute_query($accounts_connection, CHECK_ACCOUNT_EXISTS, $args);
    if (!is_last_query_success($accounts_connection)) {
        return error_result(BAD_ACCOUNT);
    }
    $result = $result -> fetchAll();
    if (empty($result)) {
        return false;
    }
    return true;
}

define("SELECT_ORDER_ID_PRICE", "SELECT id, price, resolver_id FROM orders WHERE id = ?");
function check_order_dont_resolve_and_return_order($orders_connection, $order_id) {

    $args = array($order_id + 0);
    $result = execute_query($orders_connection, SELECT_ORDER_ID_PRICE, $args);

    if(!is_last_query_success($orders_connection)) {
        return false;
    }

    $order = $result-> fetchAll(PDO::FETCH_ASSOC);
    if (!isset($order) or count($order) != 1 or !isset($order[0]) or isset($order[0][RESOLVER_ID_FIELD_NAME])) {
        return false;
    }
    return $order;
}

define('ORDERS_CONNECTION_LOGIC_PARAM', 'oconnetcion');
define('ACCOUNTS_CONNECTION_LOGIC_PARAM', 'aconnetcion');
define('TRANSACTIONS_CONNECTION_LOGIC_PARAM', 'tconnetcion');
define('CASH_LOGIC_PARAM', 'cash');
define("UPDATE_ACCOUNT_CASH_SQL", "UPDATE accounts SET cash = cash + ? WHERE id = ?");
define("UPDATE_ORDER_ID_RESOLVER_SQL", "UPDATE orders SET resolver_id = ? WHERE id = ?");
define("INSERT_TRANSACTION", "INSERT INTO transaction_log (order_id, resolver_id) VALUES (?, ?)");
function resolve_order_logic($logic_params) {
    $resolver_id = $logic_params[RESOLVER_ID_FIELD_NAME];
    $order_id = $logic_params[ORDER_ID_FIELD_NAME];
    $cash = $logic_params[CASH_LOGIC_PARAM];

    if (!isset($logic_params[ORDERS_CONNECTION_LOGIC_PARAM])) {
        return false;
    }
    $orders_connection = $logic_params[ORDERS_CONNECTION_LOGIC_PARAM];

    if (!isset($logic_params[ACCOUNTS_CONNECTION_LOGIC_PARAM])) {
        return false;
    }
    $accounts_connection = $logic_params[ACCOUNTS_CONNECTION_LOGIC_PARAM];

    if (!isset($logic_params[TRANSACTIONS_CONNECTION_LOGIC_PARAM])) {
        return false;
    }
    $transactions_connection = $logic_params[TRANSACTIONS_CONNECTION_LOGIC_PARAM];

    $args = array($order_id + 0, $resolver_id + 0);
    $transaction_update_result = execute_query($orders_connection, INSERT_TRANSACTION, $args);
    if (!is_last_query_success($transactions_connection)) {
        return false;
    }
    if ($transaction_update_result ->rowCount() != 1) {
        return false;
    }

    $args = array($resolver_id + 0, $order_id + 0);
    $orders_update_result = execute_query($orders_connection, UPDATE_ORDER_ID_RESOLVER_SQL, $args);
    if (!is_last_query_success($orders_connection)) {
        return false;
    }
    if ($orders_update_result ->rowCount() != 1) {
        return false;
    }

    $args = array($cash, $resolver_id + 0);
    $account_update_result = execute_query($accounts_connection, UPDATE_ACCOUNT_CASH_SQL, $args);
    if (!is_last_query_success($orders_connection)) {
        return false;
    }
    if ($account_update_result ->rowCount() != 1) {
        return false;
    }

    return true;
}
