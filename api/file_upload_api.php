<?php
session_start();
require_once './connect.php';
$DB = new Database();

$error = $_FILES['file']['error'];

if ($error) {
    apiError('檔案上傳出現問題');
}

$tmpname = $_FILES['file']['tmp_name'];
$type    = $_FILES['file']['type'];

$file        = fopen($tmpname, 'rb');
$fileContent = fread($file, filesize($tmpname));
fclose($file);
$fileContent = base64_encode($fileContent);

// $result = '"data:' . $type . ';base64,' . $fileContent;
// echo '<img class="comment__avatar" src=' . $result . '" />';

function apiError(string $msg)
{
    $arr = array(
        'status'  => 'error',
        'message' => $msg,
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit();
}
