<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.08.15
 * Time: 19:06
 */

include("{$_SERVER['DOCUMENT_ROOT']}/application/api/api.php");
$result = process_orders();
echo json_encode($result);
