<?php
/**
  * 微信公众平台接口
  */
 
define("TOKEN", "itdianshang");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->weixin_run(); //执行接收器方法
 
class wechatCallbackapiTest
{
	private $fromUsername;
	private $toUsername;
	private $times;
	private $keyword;
  
public function getdatearray(){
	$keyword = trim($this->keyword);
	$FromUserName = $this->fromUsername;
	$ToUserName = $this->toUsername;
	if(!empty( $keyword ))
	{
		require_once "./getdatedemo_itdianshang.php";
		$test = new getdatedemo();
		$contentStr = $test->newsinfo($keyword);
		if(!$contentStr)
        {

              $contentStr = $test->keysinfo($keyword);
				if(!$contentStr)
				{
				echo "";
				exit;
				}
		}
       return $contentStr;
	}
	else
	{
		$textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[%s]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		<FuncFlag>0</FuncFlag>
		</xml>";   
		$msgType = "text";

		require_once( dirname(__FILE__).'/weixindemoAdmin2013/topic/config/config.inc.php');
		$conn=mysql_connect($config['dbhost'],$config['dbuser'],$config['dbpass']);
		$flag=mysql_select_db($config['dbname'],$conn);
		mysql_query("set names utf8");

		$sql = mysql_query("SELECT * FROM `tb_reply_news` arc where arc.id=1");
		$nofind = mysql_fetch_array($sql);
		$title=$nofind['order'];
		$title = str_replace("&nbsp;","",$title);
		$title = str_replace("<p>","",$title);
		$title = str_replace("</p>","",$title);		
		$title = preg_replace('/^\s+\r?\n/m',"\n", $title);
		$title = htmlspecialchars_decode($title);
		$contentStr = "欢迎订阅IT电商网微信，输入m获取全部文章编号。IT电商网微信报道热点电商资讯，分享有价值的电商营销案例。这里只有电商干货，20万电商人聚集在这里。";
		$resultStr = sprintf($textTpl, $FromUserName, $ToUserName, $time, $msgType, $contentStr);
		echo $resultStr;
		exit;
	}


}   

   
public function weixin_run(){
	$this->responseMsg();

    $arr=$this->getdatearray();
    if(! is_array($arr))
    {
       $rarr[]=$arr;
	   $this->fun_xml("text",$rarr,array(0));  
    }
	elseif(is_array($arr) && $arr['type']=="news")
    {
	   $this->fun_xml("news",$arr,array($arr['numberlist'],0)); 
	}
	else
    {
       $contentStr = array("");//查找不到内容返回空
	   $this->fun_xml("text",$contentStr,array(0)); 	
	}
   }
      
   
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
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->fromUsername = $postObj->FromUserName;
			$this->toUsername = $postObj->ToUserName;
			$this->keyword = trim($postObj->Content);
		$this->times = time();
		}else {
			echo "";
			exit;
	      	}
		}
 
//微信封装类,
//type: text 文本类型, news 图文类型
//text,array(内容),array(ID)
//news,array(array(标题,介绍,图片,超链接),...小于10条),array(条数,ID)
 
private function fun_xml($type,$value_arr,$o_arr=array(0)){
 //=================xml header============
 $con="<xml>
<ToUserName><![CDATA[{$this->fromUsername}]]></ToUserName>
<FromUserName><![CDATA[{$this->toUsername}]]></FromUserName>
<CreateTime>{$this->times}</CreateTime>
<MsgType><![CDATA[{$type}]]></MsgType>";
 
      //=================type content============
switch($type){

case "text" : 
$con.="<Content><![CDATA[{$value_arr[0]}]]></Content>
<FuncFlag>{$o_arr}</FuncFlag>";  
break;
 
case "news" : 
 $con.="<ArticleCount>{$o_arr[0]}</ArticleCount>
<Articles>";
foreach($value_arr as $id=>$v){
if($id>=$o_arr[0]) break; else null; //判断数组数不超过设置数
         $con.="<item>
<Title><![CDATA[{$v[title]}]]></Title> 
<Description><![CDATA[{$v[description]}]]></Description>
<PicUrl><![CDATA[{$v[picurl]}]]></PicUrl>
<Url><![CDATA[{$v[url]}]]></Url>
</item>";
}
$con.="</Articles>
<FuncFlag>{$o_arr[1]}</FuncFlag>";  
break;
 
 } //end switch
 
//=================end return============
 echo $con."</xml>";
}
 
 
 
private function checkSignature()
{
$signature = $_GET["signature"];
$timestamp = $_GET["timestamp"];
$nonce = $_GET["nonce"]; 

$token = TOKEN;
$tmpArr = array($token, $timestamp, $nonce);
sort($tmpArr);
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
