<?php
session_start();
require_once './connect.php';
$DB = new Database();

try {

    $data   = [];
    $result = $DB->sqlExec($data, "SQL_CHK_TITLE");

    $arr = array(
        'status'  => 'success',
        'message' => '設定成功',
        'title'   => $result,
    );

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    //echo $e;
    apiError('SQL例外錯誤');
}

function apiError(string $msg)
{
    $arr = array(
        'status'  => 'error',
        'message' => $msg,
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit();
}
