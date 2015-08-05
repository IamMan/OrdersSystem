<?php
header('Content-type: json');
if (!empty($_POST)) {
    include("{$_SERVER['DOCUMENT_ROOT']}/application/api/api.php");
    $result = create_order_from_post();
    echo json_encode($result);
}
