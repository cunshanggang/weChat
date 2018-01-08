<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 22:06
 */
//定义TOKEN
define('TOKEN','james');

//把参数校验逻辑封装在checkSignature函数中
function checkSignature()
{
    //获取GET参数
    $signature = $_GET['signature'];
    $nonce = $_GET['nonce'];
    $timestamp = $_GET['timestamp'];

    //把nonce、timestamp和TOKEN组装到数组里并做排序
    $tmpArr = array($timestamp,$nonce,TOKEN);
    sort($tmpArr);

    //把数组中的元素合并成字符串
    $tmpStr = implode($tmpArr);

    //sha1加密
    $tmpStr = sha1($tmpStr);
    //判断加密后的字符串是否和signature相等
    if($tmpStr == $signature)
    {
        //相等就返回ture
        return true;
    }
    return false;
}

if(checkSignature())
{
    //获取echostr
    $echostr = $_GET['echostr'];
    if($echostr)
    {
        echo $echostr;
        exit();
    }
}


//获取POST数据
$postData = $HTTP_RAW_POST_DATA;

//判断POST数据是否为空
if(!$postData)
{
    echo "error!";
    exit;
}

//解析XML数据
$xmlObj = simplexml_load_string($postData,'SimpleXMLElement',LIBXML_NOCDATA);

if(!$xmlObj)
{
    echo "error!";
    exit;
}

//获取FromUserName
$fromUserName = $xmlObj->FromUserName;

//获取ToUserName
$toUserName = $xmlObj->ToUserName;

//获取MsgType
$msgType = $xmlObj->MsgType;

if('text' == $msgType)
{
    //用户输入的是文本，则获取用户文本的输入
    $content = $xmlObj->Content;
    //把用户输入的消息作为输出消息
    $retMsg = $content;
}
else
{
    //如果消息类型不是文本,输出错误提示消息
    $retMsg = "只支持文本消息";
}
if('text' == $msgType)
{
    //输出消息的XML模板
    $retTmp = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";
}

//对消息模板中的通配符进行替换
$resultStr = sprintf($retTmp,$fromUserName,$toUserName,time(),$retMsg);

//输出XML描述的消息
echo  $resultStr;