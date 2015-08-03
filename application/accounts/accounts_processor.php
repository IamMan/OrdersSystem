<?php

define('ACCOUNT_LOGIN_MAX_LENGTH', 50);
define('ACCOUNT_PASSWORD_MAX_LENGTH', 50);

function try_get_account_id($login, $password) {

    if (!validate_login($login) or !validate_password($password)) {
        return false;
    }
    return select_account($login, $password);

}

function validate_login($login) {
    if (isset($login))
    {
        if (strlen($login) < ACCOUNT_LOGIN_MAX_LENGTH) {
            return true;
        }
    }
    return false;
}

function validate_password($password) {
    if (isset($password))
    {
        if (strlen($password) < ACCOUNT_PASSWORD_MAX_LENGTH) {}
        return true;
    }
    return false;
}

define('SELECT_ACCOUNT', 'SELECT id FROM accounts WHERE login = ? and password = ?');
function select_account($login, $password) {
    include("{$_SERVER['DOCUMENT_ROOT']}/application/db/mydb.php");
    try {
        $account_connection = get_connect_to_accounts();
        $args = array($login, $password);
        $select_result = execute_query($account_connection, SELECT_ACCOUNT, $args);
        if (is_last_query_success($account_connection)) {
            $id = $select_result->fetchAll();
            if (isset($id[0])) {
                return $id[0][0];
            }
        }
        return false;
    } catch(Exception $ex) {
        return false;
    }

}