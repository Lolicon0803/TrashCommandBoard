<?php
session_start();

$a = $_SESSION['user_id'];
$b = $_SESSION['username'];

session_destroy();

$arr = array(
    'status'   => 'success',
    'message'  => '登出成功',
    'id_old'   => $a,
    'name_old' => $b,
    'id'       => $_SESSION['user_id'],
    'name'     => $_SESSION['username'],
);

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
