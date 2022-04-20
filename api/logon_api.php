<?php
session_start();
require_once './connect.php';
$DB = new Database();

/*
username string
password string
password2 string
uploadType int
photoLink string
$result = '"data:' . $type . ';base64,' . $fileContent;
echo '<img class="comment__avatar" src=' . $result . '" />';
 */

if (!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["password2"]) || !isset($_POST["uploadType"]) || !isset($_POST["photoLink"])) {
    apiError('請確認資料是否填妥');
}

if (strcmp($_POST["password"], $_POST["password2"])) {
    apiError('兩次輸入密碼不一致');
}

if (validation($_POST["username"]) || validation($_POST["password"]) || validation($_POST["password2"])) {
    apiError('資料含有非法字元或長度錯誤');
}

$username   = $_POST["username"];
$password   = $_POST["password"];
$password2  = $_POST["password2"];
$uploadType = $_POST["uploadType"];
$photoLink  = $_POST["photoLink"];

if ($uploadType === '0') {

    $error = $_FILES['file']['error'];

    if ($error) {
        apiError('圖片上傳出現問題');
    }

    $tmpname  = $_FILES['file']['tmp_name'];
    $type     = $_FILES['file']['type'];
    $safeType = $type === 'image/jpeg' || $type === 'image/gif' || $type === 'image/png';

    if (!$safeType) {
        apiError('圖片類型錯誤');
    }

    $file        = fopen($tmpname, 'rb');
    $fileContent = fread($file, filesize($tmpname));
    fclose($file);
    $fileContent = base64_encode($fileContent);

} else if ($uploadType === '1') {

    $url              = $photoLink;
    $image_type_check = @exif_imagetype($url);
    if ($image_type_check != IMAGETYPE_PNG && $image_type_check != IMAGETYPE_JPEG) {
        apiError('只允許PNG或JPG');
    }

    $pic      = getimagesize($url);
    $type     = $pic['mime'];
    $safeType = $type === 'image/jpeg' || $type === 'image/gif' || $type === 'image/png';

    if (!$safeType) {
        apiError('圖片類型錯誤');
    }

    $file        = file_get_contents($url);
    $fileContent = base64_encode($file);

} else {
    apiError('圖片上傳選擇錯誤');
}

try {
    // 檢驗帳號存在
    $data   = [$username];
    $result = $DB->sqlExec($data, "SQL_CHK_USERNAME");
    if ($result) {
        apiError('帳號已註冊');
    }

    // 新增帳號
    $password = password_hash($password, PASSWORD_DEFAULT);
    $data     = [$username, $password, $fileContent, $type];
    $DB->sqlExec($data, "SQL_INS_USER");

    $_SESSION['user_id']  = $DB->lastInsertId();
    $_SESSION['username'] = $username;

    $arr = array(
        'status'  => 'success',
        'message' => '註冊成功',
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    //echo $e;
    apiError('SQL例外錯誤');
}

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
