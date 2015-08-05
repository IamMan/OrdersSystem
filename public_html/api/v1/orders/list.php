<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.15
 * Time: 19:06
 */
header('Content-type: json');
include("{$_SERVER['DOCUMENT_ROOT']}/application/api/api.php");
$result = get_orders_list_from_get();
if (is_cashed($result)) {
    echo $result[INFO_FIELD_NAME];
} else {
    echo json_encode($result);
}

