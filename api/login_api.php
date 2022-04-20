<?php
session_start();
require_once './connect.php';
$DB = new Database();

if (!isset($_POST["username"]) || !isset($_POST["password"])) {
    apiError('請確認資料是否填妥');
}

if (validation($_POST["username"]) || validation($_POST["password"])) {
    apiError('資料含有非法字元或長度錯誤');
}

$username = $_POST["username"];
$password = $_POST["password"];

try {
    // 檢驗帳號存在
    $data   = [$username];
    $result = $DB->sqlExec($data, "SQL_CHK_USERNAME");
    if (!$result) {
        apiError('帳號未註冊');
    }

    $password_hash = $result['0']->password;
    if (!password_verify($password, $password_hash)) {
        apiError('帳號或密碼錯誤');
    }

} catch (PDOException $e) {

    apiError('SQL例外錯誤');
}

// set Session
$_SESSION["user_id"]  = $result['0']->id;
$_SESSION["username"] = $result['0']->username;

$arr = array(
    'status'  => 'success',
    'message' => '登入成功',
);
echo json_encode($arr, JSON_UNESCAPED_UNICODE);

// 驗證輸入
function validation($value)
{
    // 長度限制
    if (strlen($value) < 8 || strlen($value) > 16) {
        return 1;
    }

    // 字元限制
    $pattern = "/[^A-Za-z0-9+]/";
    preg_match($pattern, $value, $matches);
    if (sizeof($matches)) {
        return 2;
    }

    return 0;
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
