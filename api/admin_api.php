<?php
session_start();
require_once './connect.php';
$DB = new Database();

$user_id = $_SESSION['username'];

if ($user_id === 'adminadmin') {
    try {
        if (!isset($_POST["title"]) || empty($_POST["title"])) {
            apiError('標題有誤!');
        }

        $title  = $_POST["title"];
        $data   = [$title];
        $result = $DB->sqlExec($data, "SQL_UPD_TITLE");

        $arr = array(
            'status'  => 'success',
            'message' => '設定成功',
        );

        echo json_encode($arr, JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        //echo $e;
        apiError('SQL例外錯誤');
    }
} else {

    apiError('請取得管理員權限!');
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
