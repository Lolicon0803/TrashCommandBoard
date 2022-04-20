<?php
session_start();
require_once './connect.php';
$DB = new Database();

if (!isset($_POST["content"]) || !isset($_POST["isfile"])) {
    apiError('請確認訊息是否填妥');
}

if (empty($_POST["content"])) {
    apiError('留言不可為空');
}

if (!isset($_SESSION['user_id'])) {
    apiError('請先登入!');
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION["username"];
$content  = htmlspecialchars($_POST['content']);
$isfile   = $_POST["isfile"];

if (strlen($content) > 1000) {
    apiError('訊息過長');
}

//echo bbcode($content);

try {
    if ($isfile === '1') {

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

        $data = [$user_id, $username, $content, $fileContent];
        $DB->sqlExec($data, "SQL_INS_MSGFILE");
    } else if ($isfile === '0') {
        $data = [$user_id, $username, $content];
        $DB->sqlExec($data, "SQL_INS_MSG");
    }

    $arr = array(
        'status'  => 'success',
        'message' => '執行成功',
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo $e;
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
