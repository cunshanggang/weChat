<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12
 * Time: 13:42
 */
require_once '../app/database.php';
//获取undercover的id
$result       = $GLOBALS['database']->select("undercover",["id"],["status"=>1]);
$uid          = $result[0]['id'];
unset($result);
//获取第一个发起游戏的人的时间
$result       = $GLOBALS['database']->query("SELECT time FROM player WHERE u_id = {$uid} AND `order`=1")->fetchAll();
$startTime    = $result[0]['time'];
//获取当前时间
$time         = time();
//十分钟更新
$deadline     = strtotime($startTime) + 60*10;

if($time > $deadline) {
    $GLOBALS['database']->update("undercover",["status"=>0],["status"=>1]);
}
