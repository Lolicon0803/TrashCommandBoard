<?php
session_start();
require_once './connect.php';
$DB = new Database();

if (!isset($_POST["msg_id"])) {
    apiError('刪除訊息錯誤!');
}

if (validation($_POST["msg_id"])) {
    apiError('訊息id有誤!');
}

if (!isset($_SESSION['user_id'])) {
    apiError('請先登入!');
}

$user_id = $_SESSION['user_id'];
$msg_id  = $_POST["msg_id"];

try {

    $data = [$msg_id, $user_id];
    $DB->sqlExec($data, "SQL_DEL_MSG");

} catch (PDOException $e) {
    echo $e;
    apiError('SQL例外錯誤');
}

$arr = array(
    'status'  => 'success',
    'message' => '執行成功',
);
echo json_encode($arr, JSON_UNESCAPED_UNICODE);

function apiError(string $msg)
{
    $arr = array(
        'status'  => 'error',
        'message' => $msg,
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit();
}

// 驗證輸入
function validation($value)
{
    // 長度限制
    if (strlen($value) > 8) {
        return 1;
    }

    // 字元限制
    $pattern = "/[^0-9+]/";
    preg_match($pattern, $value, $matches);
    if (sizeof($matches)) {
        return 2;
    }

    return 0;
}
