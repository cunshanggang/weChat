<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12
 * Time: 13:42
 */
require_once '../app/database.php';
//获取undercover的id
$uid       = $GLOBALS['database']->select("undercover",["id"],["status"=>1]);
echo "<pre>";
print_r($uid);
//获取第一个发起游戏的人的时间
$startTime = $GLOBALS['database']->select("player",["time"],["u_id"=>"$uid","order"=>1]);
print_r($startTime);
//获取当前时间
$time      = time();
//十分钟更新
$deadline  = strtotime($startTime) + 3600*10;
if($time > $deadline) {
    $GLOBALS['database']->update("undercover",["status"=>0],["status"=>1]);
}
