<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.15
 * Time: 19:06
 */
header('Content-type: json');
include("{$_SERVER['DOCUMENT_ROOT']}/application/api/api.php");
$result = get_orders_deleted_from_get();
echo json_encode($result);