<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12
 * Time: 13:42
 */
require_once '../app/database.php';
$GLOBALS['database']->update("undercover",["status"=>0],["status"=>1]);