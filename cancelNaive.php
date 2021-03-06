<?php
include("connect.php");
include("token.php");


$token = $_POST["token"];
$uid = checkToken($pdo, $token);

$type = (int)$_POST["type"];
$id = (int)$_POST["id"];

$query = null;
$sql = null;
$cancel = null;

switch ($type) {
    case $TYPE_ANSWER:
        $query = $pdo->prepare("SELECT * FROM naive_answer WHERE `uid` = ? AND `aid` = ?");
        $sql = $pdo->prepare("DELETE FROM naive_answer WHERE `uid` = ? AND `aid` = ?");
        $cancel = $pdo->prepare("UPDATE answer SET `naive` = `naive` - 1 WHERE `id` = ?");
        break;
    case $TYPE_QUESTION:
        $query = $pdo->prepare("SELECT * FROM naive_question WHERE `uid` = ? AND `qid` = ?");
        $sql = $pdo->prepare("DELETE FROM naive_question WHERE `uid` = ? AND `qid` = ?");
        $cancel = $pdo->prepare("UPDATE question SET `naive` = `naive` - 1 WHERE `id` = ?");
        break;
    default:
        other_encode(400, "Too young!");
}

if ($query && $query->execute(array($uid, $id))) {
    if ($query->fetchAll(PDO::FETCH_NAMED)) {
        if ($sql->execute(array($uid, $id)) && $cancel->execute(array($id))) {
            success_encode();
        } else {
            other_encode(500, "喵喵喵");
        }
    } else {
        other_encode(400, "你没有naive这条消息");
    }
} else {
    other_encode(400, "Too simple!");
}

