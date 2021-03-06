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
            $msgType = $postObj->MsgType;//消息类型：事件或者文本
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
                                            <ArticleCount>%s</ArticleCount>
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
//                        file_put_contents("error.log",$xml.PHP_EOL,FILE_APPEND);
                        $resultStr = sprintf($xml, $fromUsername, $toUsername, $time, count($result));

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
                    case "音乐":
                        $contentStr = "亲，您已经进入了点歌模式了，请输入您喜欢的音乐前面的数字！\n\r1.Down Jacket\n\r2.your Smile";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "目录":
                        $contentStr = "请输入以下关键字获取相应的服务:\n\r1.若想查询天气,请输入:bdtq广州市\n\r2.若想查询位置,先发送定位,请输入您想查找附近1000米的地点,如:cxwz肯德基\n\r3.若想看新闻,请输入关键字:新闻\n\r4.若想听音乐:请输入关键字:音乐\n\r5.若想看冷笑话,请输入关键字:冷笑话\n\r6.若想玩脑筋急转弯,请输入关键字:脑筋急转弯\n\r7.若想玩谁是卧底游戏,确定好几个人参与之后进行输入,一般需要7个人以上,如:7人玩谁是卧底,请输入:谁是卧底7人\n\r8.若想对谁是卧底投票环节进行统计,请输入:在线投票统计\n\r9.若想玩真心话大冒险游戏,分三类,按照您的喜好可选择:真心话大冒险语音类,真心话大冒险惩罚类,真心话大冒险提问类,请输入您的喜欢类型,如:真心话大冒险语音类\n\r10.若想对中文翻译成英文,请输入:bdfy+中文,如:bdfy我想翻译成英文.\n\r11.若想抽奖,请输入:幸运大转盘";
                        $msgType = 'text';
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "1":
                        $description = "Down Jacket";
                        $musicUrl = "http://39.108.108.194/weChat/public/upload/audio/DownJacket.mp3";
                        $title = "James Lin";
                        $resultStr = sprintf($this->musicTpl(), $fromUsername, $toUsername, $time, $title, $description, $musicUrl, $musicUrl);
                        echo $resultStr;
                        break;
                    case "2":
                        $description = "Your Smile";
                        $musicUrl = "http://39.108.108.194/weChat/public/upload/audio/yourSmile.mp3";
                        $title = "村上岗音乐集合";
                        $resultStr = sprintf($this->musicTpl(), $fromUsername, $toUsername, $time, $title, $description, $musicUrl, $musicUrl);
                        echo $resultStr;
                        break;
                    case "幸运大转盘":
                        $contentStr = "请点击一下链接进行抽奖:\r\nhttp://39.108.108.194/weChat/app/spinwin/index.html";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "在线投票统计":
                        $contentStr = "请点击一下链接进行统计:\r\nhttp://39.108.108.194/weChat/app/vote/index.html";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "脑筋急转弯":
                        $id = rand(1,225);
                        //注：$id要加“”，否则查询不到数据。
                        $row = $GLOBALS['database']->select("brain_teaser","*",["id"=>"$id"]);
                        $contentStr = "脑筋急转弯：\n\r"."问题：".$row[0]['question']."\n\r"
                            ."答案：".$row[0]['answer'];
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "冷笑话":
                        $id = rand(1,80);
                        $row = $GLOBALS['database']->select("dry_humor","*",["id"=>"$id"]);
                        $contentStr = "冷笑话：\n\r".$row[0]['content'];
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "真心话大冒险语音类":
                        $ids = $GLOBALS['database']->select("true_or_dare","id",["type"=>1]);
                        $rand = $ids[array_rand($ids,1)];
                        $r = $GLOBALS['database']->select("true_or_dare","*",["and"=>["id"=>"$rand","type"=>"1"]]);
                        $content = $r[0]['content'];
                        $id = $r[0]['id'];
                        $type = $r[0]['type'];
                        $wxname = $fromUsername;
                        $time = date("Y-m-d H:i:s");
                        //次数
                        $times = ($r[0]['times']+1);
                        //插入到true_player表中
                        $GLOBALS['database']->insert("true_player",["u_id"=>"$rand","wxname"=>"$wxname","content"=>"$content","time"=>"$time"]);
                        //更新ture_or_dare表的次数
                        $GLOBALS['database']->update("true_or_dare",["times"=>"$times"],["id"=>"$rand"]);
                        //返回结果给用户
                        $contentStr = "真心话大冒险语音类:\n\r"."http://39.108.108.194/weChat/app/trueOrDare/index.html?type=$type&id=$id";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "真心话大冒险提问类":
                        $ids = $GLOBALS['database']->select("true_or_dare","id",["type"=>2]);
                        $rand = $ids[array_rand($ids,1)];
                        $r = $GLOBALS['database']->select("true_or_dare","*",["and"=>["id"=>"$rand","type"=>"2"]]);
                        $content = $r[0]['content'];
                        $id = $r[0]['id'];
                        $type = $r[0]['type'];
                        $wxname = $fromUsername;
                        $time = date("Y-m-d H:i:s");
                        //次数
                        $times = ($r[0]['times']+1);
                        //插入到true_player表中
                        $GLOBALS['database']->insert("true_player",["u_id"=>"$rand","wxname"=>"$wxname","content"=>"$content","time"=>"$time"]);
                        //更新ture_or_dare表的次数
                        $GLOBALS['database']->update("true_or_dare",["times"=>"$times"],["id"=>"$rand"]);
                        //返回结果给用户
                        $contentStr = "真心话大冒险提问类:\n\r"."http://39.108.108.194/weChat/app/trueOrDare/index.html?type=$type&id=$id";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "真心话大冒险惩罚类":
                        $ids = $GLOBALS['database']->select("true_or_dare","id",["type"=>3]);
                        $rand = $ids[array_rand($ids,1)];
                        $r = $GLOBALS['database']->select("true_or_dare","*",["and"=>["id"=>"$rand","type"=>"3"]]);
                        $content = $r[0]['content'];
                        $id = $r[0]['id'];
                        $type = $r[0]['type'];
                        $wxname = $fromUsername;
                        $time = date("Y-m-d H:i:s");
                        //次数
                        $times = ($r[0]['times']+1);
                        //插入到true_player表中
                        $GLOBALS['database']->insert("true_player",["u_id"=>"$rand","wxname"=>"$wxname","content"=>"$content","time"=>"$time"]);
                        //更新ture_or_dare表的次数
                        $GLOBALS['database']->update("true_or_dare",["times"=>"$times"],["id"=>"$rand"]);
                        //返回结果给用户
                        $contentStr = "真心话大冒险惩罚类:\n\r"."http://39.108.108.194/weChat/app/trueOrDare/index.html?type=$type&id=$id";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    case "谁是卧底":
                        $contentStr = "谁是卧底:\n\r"."http://39.108.108.194/weChat/app/undercover/index.html\n\r谁是卧底规则:\n\r游戏人数：最好7个游戏者，1个卧底，若干不明的围观者。
游戏规则：
1、在场7个人中6个人拿到相同的一个词语，剩下的1个拿到与之相关的另一个词语；
2、每人每轮只能说一句话描述自己拿到的词语（不能直接说出来那个词语），也不能让卧底发现，也要给同胞以暗示。
3、每轮描述完毕，7人投票选出怀疑是卧底的那个人，得票数最多的人出局，俩个人一样多的话，待定（就是保留）。
4、若有卧底撑到剩下最后三人，则卧底获胜，反之，则大部队获胜。
5、选择词语的话要选择有关的词语好，比如胡子和眉毛，猪肉和牛肉等等。";
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
//                    default:
//                        $contentStr = "亲,请输入关键字哦".$this->emoji($emoji_str = "/::D");
//                        $msgType = "text";
//                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                        echo $resultStr;
//                        break;
                }
            //需要正则匹配的关键字 start -----
//                preg_match("/^(cxwz)([\x{4e00}-\x{9fa5}]+)/ui",$keyword,$r);
                preg_match("/^([a-z]{4})([\x{4e00}-\x{9fa5}]+.*)/ui",$keyword,$r);
                //字符：cxwz肯德基 返回结果：$r[0]=cxwz肯德基,$r[1]=cxwz,$r[2]=肯德基
                switch($r[1]) {
                    //查询位置
                    case 'cxwz':
                        //查询用户的地址
                        $row =  $GLOBALS['database']->select("members","*",['wxname'=>"$fromUsername"]);
                        //拼接链接地址
                        $head = "请点击该链接可查到附近【".$r[2]."】的信息:\n\r";
                        $link = "http://api.map.baidu.com/place/search?query=".urlencode($r[2])."&location=".$row[0]['latitude'].','.$row[0]['longitude']."&radius=1000&output=html&coord_type=gcj02";
                        $contentStr = $head.$link;
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    //查询天气
                    case 'cxtq':
                        $url = "http://www.sojson.com/open/api/weather/json.shtml?city=$r[2]";
                        $res = $this->cURL($url);
                        $contentStr = "昨天:\n".$res['data']['yesterday']['date']."\n".'日出:'.$res['data']['yesterday']['sunrise']."\n".'最高温:'.$res['data']['yesterday']['high']."\n".'最低温:'.$res['data']['yesterday']['low']."\n".'日落:'.$res['data']['yesterday']['sunset']."\n".'空气质量:'.$res['data']['yesterday']['aqi']."\n".'风向:'.$res['data']['yesterday']['fx']."\n".'风级:'.$res['data']['yesterday']['fl']."\n".'类型:'.$res['data']['yesterday']['type']."\n".'温馨提示:'.$res['data']['yesterday']['notice']."\n\r";
                        foreach($res['data']['forecast'] as $k=>$v) {
                            $contentStr.= $v['date']."\n".'日出:'.$v['sunrise']."\n".'最高温:'.$v['high']."\n".'最低温:'.$v['low']."\n".'日落:'.$v['sunset']."\n".'空气质量:'.$v['aqi']."\n".'风向:'.$v['fx']."\n".'风级:'.$v['fl']."\n".'类型:'.$v['type']."\n".'温馨提示:'.$v['notice']."\n\r";
                        }
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    //百度翻译
                    case 'bdfy':
                        $res = $this->translate($r[2]);
                        $contentStr = $r[2]."\n".$res['trans_result']['0']['dst'];
                        $msgType = "text";
                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                        break;
                    //百度天气
                    case 'bdtq':
                        $url = "http://api.map.baidu.com/telematics/v3/weather?location=$r[2]&output=json&ak=A97e3bda2ac739aa574f16ec94055d75";
                        $result = $this->cURL($url);
                        //拼装字符串
                        //头部
                        $contentHead = "<xml>
                                            <ToUserName><![CDATA[%s]]></ToUserName>
                                            <FromUserName><![CDATA[%s]]></FromUserName>
                                            <CreateTime>%s</CreateTime>
                                            <MsgType><![CDATA[news]]></MsgType>
                                            <ArticleCount>%s</ArticleCount>
                                            <Articles>";
                        //中部
                        $items = "<item>
                                     <Title><![CDATA[%s]]></Title>
                                     <Description><![CDATA[%s]]></Description>
                                     <PicUrl><![CDATA[%s]]></PicUrl>
                                     <Url><![CDATA[%s]]></Url>
                                 </item>";
                        $t = $r[2]."天气实况与预报";
                        $contentBody = sprintf($items,$t,"","","");
                        foreach($result['results'][0]['weather_data'] as $k=>$v) {
                            $title = $v['date']. "\n".$v['weather']." ".$v['wind']." ".$v['temperature'];
                            $contentBody.=sprintf($items,$title,"",$v['dayPictureUrl'],"");
                        }
                        //底部
                        $contentFooter = "$contentBody</Articles></xml>";
                        //合并
                        $xml = $contentHead.$contentBody.$contentFooter;
                        $c = (count($result['results'][0]['weather_data'])+1);
                        $resultStr = sprintf($xml, $fromUsername, $toUsername, $time, $c);
                        echo $resultStr;
                        break;
                }
            //需要正则匹配的关键字 end -----
            //谁是卧底游戏 start
                //匹配谁是卧底
                preg_match("/^([\x{4e00}-\x{9fa5}]+)([\d]+)/u",$keyword,$match);
                switch ($match[1]) {
                    case '谁是卧底':
                        //第n个获取信息的人
                        $chk_res = $GLOBALS['database']->select("undercover","*",["status"=>1]);
                        $join_time = date("Y-m-d H:i:s");
                        $id = $chk_res[0]['id'];
                        if($chk_res) {
                            $uid = $chk_res[0]['id'];
                            $chk_name = $GLOBALS['database']->select("player","*",array("AND"=>array("wxname"=>"$fromUsername","u_id"=>"$uid")));
                            $last_order_id = $GLOBALS['database']->select("player","*",array("u_id"=>"$uid","ORDER"=>["id"=>"DESC"],"LIMIT"=>"1"));
                            //判断是否同一个人在同一个游戏中获取两次信息
                            if(empty($chk_name)) {
                                if($last_order_id[0]['order'] == ($chk_res[0]['luck_number']-1) && ($last_order_id[0]['order'] != ($match[2]-1))) {
                                    //echo "你是卧底";
                                    $order = $chk_res[0]['luck_number'];
                                    $role_name = $chk_res[0]['spy'];
                                    $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>"0","role_name"=>"$role_name","u_id"=>"$uid","time"=>"$join_time","order"=>"$order"]);

                                    $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($chk_res[0]['spy'])."&type=0"."&order=$order"."&join_time=".urlencode($join_time);
                                    $msgType = 'text';
                                    $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                    echo $resultStr;
                                }else if(($last_order_id[0]['order'] == ($match[2]-1)) && ($last_order_id[0]['order'] != ($chk_res[0]['luck_number']-1))){
                                    //最后一个取词语，但不是卧底
                                    $order = $last_order_id[0]['order']+1;
                                    $role_name = $chk_res[0]['folk'];
                                    $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>"1","role_name"=>"$role_name","u_id"=>"$uid","time"=>"$join_time","order"=>"$order"]);
                                    //最后一个取完号码，更新undercover表的状态
                                    $GLOBALS['database']->update("undercover",["status"=>"0","luck_number"=>"0"],["id"=>"$id"]);

                                    $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($chk_res[0]['folk'])."&type=1"."&order=$order"."&join_time=".urlencode($join_time);
                                    $msgType = 'text';
                                    $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                    echo $resultStr;
                                }else if($last_order_id[0]['order'] == ($chk_res[0]['luck_number']-1) && ($last_order_id[0]['order'] == ($match[2]-1))){
                                    //既是卧底，又是最后一个人
                                    $order = $chk_res[0]['luck_number'];
                                    $role_name = $chk_res[0]['spy'];
                                    $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>"0","role_name"=>"$role_name","u_id"=>"$uid","time"=>"$join_time","order"=>"$order"]);
                                    $GLOBALS['database']->update("undercover",["status"=>"0","luck_number"=>"0"],["id"=>"$id"]);

                                    $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($chk_res[0]['spy'])."&type=0"."&order=$order"."&join_time=".urlencode($join_time);
                                    $msgType = 'text';
                                    $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                    echo $resultStr;
                                }else{
                                    //平民，又不是最后一个人
                                    $order = $last_order_id[0]['order']+1;
                                    $role_name = $chk_res[0]['folk'];
                                    $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>"1","role_name"=>"$role_name","u_id"=>"$uid","time"=>"$join_time","order"=>"$order"]);
                                    $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($chk_res[0]['folk'])."&type=1"."&order=$order"."&join_time=".urlencode($join_time);
                                    $msgType = 'text';
                                    $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                    echo $resultStr;
                                }
                            }else{
                                //非法获取
                                $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?type=2&join_time=".urlencode($join_time);
                                $msgType = 'text';
                                $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                echo $resultStr;
                            }

                        }else{
                            //第一次创建的人
                            //获取词组表undercover的总数
                            $count = $GLOBALS['database']->count("undercover","*");
                            //随机抽取一个词组的id
                            $rand = rand(1,$count);
                            //查询
                            $spy = $GLOBALS['database']->select("undercover","*",["id"=>"$rand"]);
                            //卧底号码
                            $luck_number = rand(1,$match[2]);
                            //角色名称
                            $role_name_spy = $spy[0]['spy'];//卧底
                            $role_name_folk = $spy[0]['folk'];
                            $u_id = $spy[0]['id'];
                            //更新选中的状态
                            $GLOBALS['database']->update("undercover",["status"=>1,"luck_number"=>"$luck_number"],["id"=>"$rand"]);
                            //插入数据到玩家player表,判断他是否为卧底,1:平民，0:卧底
                            if($luck_number == 1) {
                                $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>0,"role_name"=>"$role_name_spy","u_id"=>"$u_id","time"=>"$join_time","order"=>1]);
                                $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($spy[0]['spy'])."&type=0"."&order=1"."&join_time=".urlencode($join_time);
                                $msgType = 'text';
                                $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                echo $resultStr;
                            }else{
                                $GLOBALS['database']->insert("player",["wxname"=>"$fromUsername","role"=>1,"role_name"=>"$role_name_folk","u_id"=>"$u_id","time"=>"$join_time","order"=>1]);
                                $contentStr = "http://39.108.108.194/weChat/app/undercover/index.html?keyword=".urlencode($spy[0]['folk'])."&type=1"."&order=1"."&join_time=".urlencode($join_time);
                                $msgType = 'text';
                                $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                echo $resultStr;
                            }
                        }
//                        $msgType = 'text';
//                        $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                        echo $resultStr;
                        break;
                }
            //谁是卧底游戏 end
            }
            //关注subscribe,取消关注unsubscribe
            if($event == 'subscribe') {
                //插入到follow表
                $time = date("Y-m-d H:i:s");
                $wxname = $fromUsername;
                $r = $GLOBALS['database']->select("follower","*",["wxname"=>"$wxname"]);
                if(empty($r)){
                    $GLOBALS['database']->insert("follower",["wxname"=>"$wxname","time"=>"$time"]);
                }
                $contentStr = "Hi,欢迎关注jamess公众号，期待您的到来!请输入以下关键字获取相应的服务:\n\r1.若想查询天气,请输入:cxtq广州市或bdtq广州市\n\r2.若想查询位置,先发送定位,请输入您想查找附近1000米的地点,如:cxwz肯德基\n\r3.若想看新闻,请输入关键字:新闻\n\r4.若想听音乐:请输入关键字:音乐\n\r5.若想看冷笑话,请输入关键字:冷笑话\n\r6.若想玩脑筋急转弯,请输入关键字:脑筋急转弯\n\r7.若想玩谁是卧底游戏,确定好几个人参与之后进行输入,一般需要7个人以上,如:7人玩谁是卧底,请输入:谁是卧底7人\n\r8.若想对谁是卧底投票环节进行统计,请输入:在线投票统计\n\r9.若想玩真心话大冒险游戏,分三类,按照您的喜好可选择:真心话大冒险语音类,真心话大冒险惩罚类,真心话大冒险提问类,请输入您的喜欢类型,如:真心话大冒险语音类\n\r10.若想对中文翻译成英文,请输入:bdfy+中文,如:bdfy我想翻译成英文.\n\r11.若想抽奖,请输入:幸运大转盘";
                $msgType = 'text';
                $resultStr = sprintf($this->textTpl(), $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            }

            //发送地理位置
            if($msgType == 'location') {
                //经度
                $Location_Y = $postObj->Location_Y;
                //纬度
                $Location_X = $postObj->Location_X;
                //--- 数据入库 start ---
                $r = $GLOBALS['database']->select("members","*",['wxname'=>"$fromUsername"]);
                //注：这里的经度和纬度的数字要用双引号“”,否则无法添加进去.
                $time = time();
                if($r) {
                    $GLOBALS['database']->update("members",['longitude'=>"$Location_Y",'latitude'=>"$Location_X",'join_time'=>$time],['wxname'=>"$fromUsername"]);
                }else{
                    $GLOBALS['database']->insert("members",['longitude'=>"$Location_Y",'latitude'=>"$Location_X",'join_time'=>$time,'wxname'=>"$fromUsername"]);
                }
                //--- 数据入库 end ---
                $contentStr = "亲，我们已经收到您发送的地理位置了\n\r经度:{$Location_Y}\n\r纬度:{$Location_X}\n\r请输入您关心的地方,即可查询!如:cxwz肯德基";
                $msgType = "text";
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
    //音频模板
    public function musicTpl() {
        $tpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>
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

    //cURL
    public function cURL($url) {
        $chjk = curl_init('http://404.php.net/');//初始化一个curl会话
        curl_setopt($chjk,CURLOPT_URL,$url);//设置curl会话的接口地址
        curl_setopt($chjk,CURLOPT_CUSTOMREQUEST,"GET");//设置请求方式为GET
        curl_setopt($chjk,CURLOPT_RETURNTRANSFER,1);//设置CURLOPT_RETURNTRANSFER为1，表示如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE
        curl_setopt($chjk,CURLOPT_HEADER,0);
        $result = curl_exec($chjk);

        return json_decode($result,true);
    }

    //----------- 百度翻译start --------------
    //翻译入口
    public function translate($query, $from="zh", $to="en"){
        $args = array(
            'q' => $query,
            'appid' => '20180111000114219',
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,

        );
        $args['sign'] = $this->buildSign($query, "20180111000114219", $args['salt'], "xLBpGcIBNXbm38pks45p");
        $ret = $this->call("http://api.fanyi.baidu.com/api/trans/vip/translate", $args);
        $ret = json_decode($ret, true);
        return $ret;
    }

    //加密
    public function buildSign($query, $appID, $salt, $secKey) {/*{{{*/
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }/*}}}*/

    //发起网络请求
    public function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array()){/*{{{*/
        $ret = false;
        $i = 0;
        while($ret === false)
        {
            if($i > 1)
                break;
            if($i > 0)
            {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }/*}}}*/

    public function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = 10, $headers=array()){/*{{{*/
        $ch = curl_init();
        if($method == "post")
        {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else
        {
            $data = $this->convert($args);
            if($data)
            {
                if(stripos($url, "?") > 0)
                {
                    $url .= "&$data";
                }
                else
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }/*}}}*/

    public function convert(&$args)
    {/*{{{*/
        $data = '';
        if (is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val)."&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }/*}}}*/
    //----------- 百度翻译end ----------------
}
?>