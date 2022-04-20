<?php
session_start();
require_once './connect.php';
$DB = new Database();

if (!isset($_POST["msg_id"]) || !isset($_POST["all_msg"])) {
    apiError('查找訊息錯誤!');
}

if (validation($_POST["msg_id"]) || validation($_POST["all_msg"])) {
    apiError('訊息id有誤!');
}

$msg_id  = $_POST["msg_id"];
$all_msg = $_POST["all_msg"];
$result;

try {

    if ($all_msg === '0') {
        $data   = [$msg_id];
        $result = $DB->sqlExec($data, "SQL_CHK_MSG");
    } else if ($all_msg === '1') {
        $data   = [];
        $result = $DB->sqlExec($data, "SQL_CHK_OMSG");
    }

    if (!$result) {
        apiError('查無此留言!');
    }

    for ($i = 0; $i < count($result); $i++) {
        $result[$i]            = (array) $result[$i];
        $result[$i]['content'] = bbcode($result[$i]['content']);
    }

    $arr = array(
        'status'  => 'success',
        'message' => '查找成功',
        'data'    => $result,
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

function bbcode($bbcode, $reps = array())
{
    assert($bbcode != null && $bbcode != '');

    $bbcode = htmlentities($bbcode);
    $bbcode = "<p>$bbcode</p>";
    $bbcode = preg_replace('/^\s*$/m', '</p><p>', $bbcode);

    // add built-in regex replacements to perform (i.e. core bbcode tags)
    $reps['{\[b\](.+?)\[/b\]}is']               = "<b>$1</b>\n"; // [b] - bold
    $reps['{\[i\](.+?)\[/i\]}is']               = "<i>$1</i>\n"; // [i] - italic
    $reps['{\[u\](.+?)\[/u\]}is']               = "<u>$1</u>\n"; // [u] - underline
    $reps['{\[color=(.+?)\](.*?)\[/color\]}is'] = "<span style='color:$1;'>$2</span>\n"; // [color]
    $reps['{\[img\](.*?)\[/img\]}is']           = "<img src='$1' alt=''/>\n"; // [img]

    foreach ($reps as $regex => $replace) {
        $bbcode = preg_replace($regex, $replace, $bbcode);
    }

    return $bbcode;

}
