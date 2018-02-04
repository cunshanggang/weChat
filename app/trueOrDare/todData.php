<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 9:46
 */
require_once '../database.php';
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//echo $_POST['type'].$_POST['id'];
//$database = new medoo([
//    //必需
//    'database_type'=>'mysql',
//    'database_name'=>'tp',      //数据库名称
//    'server'=>'localhost',       //数据库连接地址
//    'username'=>'root',  //用户名
//    'password'=>'',  //密码
//    'charset'=>'utf8',            //数据库编码
//    // [可选的] 数据库连接端口
//    'port'=> 3306,
//    // [可选]表前缀
//    'prefix'=>'',
//    // [可选]用于连接的driver_option，阅读更多从http://www.php.net/manual/zh/pdo.setattribute.php
//    'option'=> [PDO :: ATTR_CASE => PDO :: CASE_NATURAL]
//]);
//$id = $_POST['type'];
//$type = $_POST['id'];

$id = empty($_POST['id'])? 0:$_POST['id'];
$type = $_POST['type'];

$r = $GLOBALS['database']->select("true_or_dare","*",["AND"=>["type"=>"$type","id"=>"$id"]]);
//echo "<pre>";
//echo print_r($r);
//echo "</pre>";
echo json_encode($r);


