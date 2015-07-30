<?php
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