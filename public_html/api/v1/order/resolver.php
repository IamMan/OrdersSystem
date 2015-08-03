<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

//header('Content-type: json');
if (!empty($_GET)) {
    include("{$_SERVER['DOCUMENT_ROOT']}/application/api/api.php");

    $result = resolve_order_from_get();
    echo json_encode($result);
}

