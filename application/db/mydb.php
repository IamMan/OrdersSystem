<?php

include('db_configs.php');

function check_pdo_nd() {
    $mysqlnd = function_exists('mysqli_fetch_all');

    $res = null;
    if ($mysqlnd) {
        $res = 'mysqlnd enabled! ';
    }

    $pdo = new PDO('mysql:host=myapp;dbname=database', 'username', 'password');
    if (strpos($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd') !== false) {
        $res = $res.'PDO MySQLnd enabled!';
    }

    return $res;
}

function get_connect_to_orders() {
    $connection = new PDO(ORDERS_CONNECTION, ORDERS_USER_NAME, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
}

function get_connect_to_accounts() {
    $connection = new PDO(ORDERS_CONNECTION, ACCOUNTS_CONNECTION, ORDERS_USER_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
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
        //TODO: LOG IT!!!
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

function connect() {

    $dbName = 'ordersdb';
    $dbHost = 'localhost';
    $dbUserName = 'mm';
    $dbUserPassword = 'superman';

    $cont = null;

    if (null == $cont) {
        try {
            $cont = new PDO("mysql:host=" . $dbHost . ";" . "dbname=" . $dbName, $dbUserName, $dbUserPassword);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    return $cont;
}




?>