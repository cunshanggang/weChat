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

class wechatCallbackapiTest {
    //验证token
    public function valid() {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg() {
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
//            $msgType = $postObj->MsgType;//消息类型：事件或者文本
            $event = $postObj->Event;//事件类型，subscribe(订阅),unsubscribe(取消订阅)
            $keyword = trim($postObj->Content);
            $time = time();

            if(!empty($keyword)) {
                switch ($keyword) {
                    case "姚明":
                        $contentStr = "姚明！要命！饶命！".$this->emoji($emoji_str = "/::)");
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    default:
                        $contentStr = "请输入关键字";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                }
            }
            ////关注subscribe,取消关注unsubscribe
            if($event == 'subscribe') {
                $contentStr = "Hi,欢迎关注jamess公众号，期待您的到来!";
                $msgType = 'text';
                $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            }

        } else {
            echo "";
            exit;
        }
    }

    public function textTpl() {
        $textTpl = "<xml>  
                    <ToUserName><![CDATA[%s]]></ToUserName>  
                    <FromUserName><![CDATA[%s]]></FromUserName>  
                    <CreateTime>%s</CreateTime>  
                    <MsgType><![CDATA[%s]]></MsgType>  
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        return $textTpl;
    }
    private function checkSignature() {
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

    //解析表情
    public function emoji($emoji_str) {
        //将字符串组合成json格式
        $emoji_str = '["'.$emoji_str.'"]';
        $emoji_arr = json_decode($emoji_str, true);
        if (count($emoji_arr) == 1)
            return $emoji_arr[0];
        else
            return null;
    }
}
?>