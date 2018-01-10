<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:44
 */
/**
 * wechat php test
 */
//define your token
define("TOKEN", "james");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//exit;
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //ini_set('always_populate_raw_post_data',-1);
//        $postStr = file_get_contents("php://input");
        //file_put_contents("error.log",$postStr.PHP_EOL,FILE_APPEND);

        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>  
<ToUserName><![CDATA[%s]]></ToUserName>  
<FromUserName><![CDATA[%s]]></FromUserName>  
<CreateTime>%s</CreateTime>  
<MsgType><![CDATA[%s]]></MsgType>  
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";
            if(!empty( $keyword ))
            {
                $msgType = "text";
                if($keyword == "姚明") {
                    $contentStr = "姚明！要命！饶命！";
                }else{
                    preg_match("/(\d+)([+-])(\d+)/i",$keyword,$res);
                    switch ($res[2]) {
                        case '+';
                            $result = $res[1]+$res[3];
                            break;
                        case '-';
                            $result = $res[1]-$res[3];
                            break;
                    }
//                    if($res[2] == '+') {
//                        $result = $res[1]+$res[3];
//                    }else if($res[2] == '-') {
//                        $result = $res[1]-$res[3];
//                    }
                    $contentStr = "运算结果是:".$result;
                }
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            }else{
                echo "Input something...";
                exit;
            }
        }
        else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>