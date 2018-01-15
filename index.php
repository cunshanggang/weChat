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
require_once 'app/database.php';
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
//        file_put_contents("error.log",$postStr.PHP_EOL,FILE_APPEND);

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
            //关键字的处理
            if(!empty($keyword)) {
                switch ($keyword) {
                    case "姚明":
                        $contentStr = "姚明！要命！饶命！".$this->emoji($emoji_str = "/::)");
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    //回复图文消息
                    case "新闻":
                        $result = $GLOBALS['database']->select("news","*",["LIMIT"=>10]);
//                        file_put_contents("error.log",$result[0]['title'].PHP_EOL,FILE_APPEND);

                        //拼装字符串
                        //头部
                        $contentHead = "<xml>
                                            <ToUserName><![CDATA[%s]]></ToUserName>
                                            <FromUserName><![CDATA[%s]]></FromUserName>
                                            <CreateTime>%s</CreateTime>
                                            <MsgType><![CDATA[news]]></MsgType>
                                            <ArticleCount>count($result)</ArticleCount>
                                            <Articles>";
                        //中部
                        $items = "<item>
                                    <Title><![CDATA[%s]]></Title>
                                     <Description><![CDATA[%s]]></Description>
                                     <PicUrl><![CDATA[%s]]></PicUrl>
                                     <Url><![CDATA[%s]]></Url>
                                 </item>";
                        $contentBody = "";
                        foreach($result as $k=>$v) {
                            $contentBody.=sprintf($items,$v['title'],$v['description'],$v['picUrl'],$v['url']);
                        }
                        //底部
                        $contentFooter = "$contentBody</Articles></xml>";
                        //合并
                        $xml = $contentHead.$contentBody.$contentFooter;
                        file_put_contents("error.log",$xml.PHP_EOL,FILE_APPEND);
                        $resultStr = sprintf($xml, $fromUsername, $toUsername, $time);

                        /*
                        //新闻一
                        $title1 = "标题1";
                        $desc1 = "内容1";
                        $picUrl1 = "http://39.108.108.194/weChat/public/upload/img/news1.jpg";
                        $url1 = "http://news.baidu.com";
                        //新闻二
                        $title2 = "标题2";
                        $desc2 = "内容2";
                        $picUrl2 = "http://39.108.108.194/weChat/public/upload/img/news2.jpg";
                        $url2 = "http://news.qq.com";

//                        $msgType = "news";
                        $resultStr = sprintf($this->picTextTpl(), $fromUsername, $toUsername, $time, $title1, $desc1, $picUrl1, $url1, $title2, $desc2, $picUrl2, $url2);
                        */
                        echo $resultStr;
                        break;
                    default:
                        $contentStr = "亲,请输入关键字哦".$this->emoji($emoji_str = "/::D");
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                }
            }
            //关注subscribe,取消关注unsubscribe
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

    //图文模板
    public function picTextTpl() {
        $tpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>2</ArticleCount>
                    <Articles>
                            <item>
                                <Title><![CDATA[%s]]></Title>
                                 <Description><![CDATA[%s]]></Description>
                                 <PicUrl><![CDATA[%s]]></PicUrl>
                                 <Url><![CDATA[%s]]></Url>
                            </item>
                            <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>
                    </Articles>
                </xml>";
        return $tpl;
    }
    //文本模板
    public function textTpl() {
        $tpl = "<xml>  
                    <ToUserName><![CDATA[%s]]></ToUserName>  
                    <FromUserName><![CDATA[%s]]></FromUserName>  
                    <CreateTime>%s</CreateTime>  
                    <MsgType><![CDATA[%s]]></MsgType>  
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                </xml>";
        return $tpl;
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