<?php
header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__)."/include/lib_splitword_full.php");
class getdatedemo{

public function newsinfo($keyword){
	$keyword=strtolower($keyword);//转换为小写
    $this->dbsql(); 
    $krows=array();

    $keywordssql=mysql_query("select `aid`,`pipei` from `dede_addonweixin10` where `pipei` !='' and typeid='12'");
	while($krow=mysql_fetch_array($keywordssql))
	{
	   if($krow)
		{
	      $krows[]=$krow;
	    }
	}

	foreach($krows as $a)
    {
	  $pipei=trim($a['pipei']);
	  $pipeiarray=explode(",",$pipei);
	  if($pipeiarray) 
		{
	       foreach($pipeiarray as $b)
			{
		        
				if($keyword===$b)
                 $ids[]=$a['aid'];
		    }
	    }
	}

	$ids= array_unique($ids);
	if($ids)
    {
	  $idstring=implode(",",$ids);
	  $sql=mysql_query("SELECT title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag FROM `dede_addonweixin10` arc where arc.aid in ($idstring) and arc.typeid='12' order by arc.flag desc,arc.aid desc limit 6");

		while($row2 = mysql_fetch_array($sql))
		{
           if($row2['shuxing']=="文字")
           {
					$stringfind=$row2['zhaiyao'];		
				    $rows2=$stringfind;
		   }
		   else
           { 
			   if($row2)
				{
				  $row2['title']=trim($row2['title']);
				  $row2['description']=trim($row2['zhaiyao']);
				  $row2['picurl']="http://weixin.kuqin.com".$row2['weixinpic']; 
				  $row2['url']=$row2['weixinurl'];
				  $rows2[]=$row2;
				  $rows2['leixing']='news';
				}
			}
		}
		if($rows2)
		{
          if($rows2['leixing']=='news')
		  {
			  $numberlist2=count($ids);
			  $rows2['numberlist']=$numberlist2;
			  $rows2['type']='news';
		  }

		}

	}

	return $rows2;	
	exit(0);
}

public function keysinfo($keyword){
	$keyword=strtolower($keyword);//转换为小写
    $this->dbsql(); 
    $keyword = strip_tags($keyword);
    if($keyword == ""){
            exit(0);
    }

    $keyword = iconv("UTF-8","GBK", $keyword);
    $tagkeyword = $keyword;//Tag标签所用
    if(strlen($keyword)>7)
    {
            $sp = new SplitWord();
            $keywords=$sp->SplitRMM($keyword);
            $sp->Clear();
    }
    else
    {
            $keywords = $keyword;
    }

    if($keywords)
    {
    $addsql=$this->GetKeywordSql($keywords);
    }
    else
    {
    $addsql='1';
    }

    $addsql = iconv("GBK","UTF-8", $addsql);
    $tagid=$this->GetTagid($tagkeyword);

$aaa="select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where $addsql  and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp1 union select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where  FIND_IN_SET($tagid, arc.tag)>0 and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp2 limit 8";
echo $aaa;
exit;
    
	  $sql5=mysql_query("select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where $addsql  and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp1 union select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where  FIND_IN_SET($tagid, arc.tag)>0 and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp2 limit 8");

		while($row3=mysql_fetch_array($sql5))
		{

            if($row3)
                {
                  $row3['title']=trim($row3['title']);
                  $row3['description']=trim($row3['zhaiyao']);
                  $row3['picurl']="http://weixin.kuqin.com".$row3['weixinpic'];
                  $row3['url']=$row3['weixinurl'];
                  $rows3[]=$row3;
                }
        }
        if($rows3)
        {
            $numberlist3=count($rows3);
            $rows3['numberlist']=$numberlist3;
            $rows3['type']='news';
        }

    return $rows3;
    exit(0);
}

function GetKeywordSql($Keywords)
{
    $ks = explode(' ',$Keywords);
    $kwsql = '';
    $kwsqls = array();
    foreach($ks as $k)
    {
        $k = trim($k);
        if(strlen($k)<1)
        {
            continue;
        }
        if(ord($k[0])>0x80 && strlen($k)<2)
        {
            continue;
        }
        $k = addslashes($k);
        $kwsqls[] = " arc.title LIKE '%$k%' ";
    }
    if(!isset($kwsqls[0]))
    {
        return '';
    }
    else
    {
        $kwsql = join(' And ',$kwsqls);
        return $kwsql;
    }
}

function GetTagid($tagkeyword)//获取标签
{
    $this->dbsql();
	$strfield=$tagkeyword;
	$strfield = iconv("GBK","UTF-8", $strfield);
    $tagid = '';

    $sql=mysql_query("select `id`,`keyname` from `tb_customtags` where 1 order by sort");
	while($r=mysql_fetch_array($sql))
	{
	   if($r)
		{
	      $rs[]=$r;
	    }
	}

	foreach($rs as $v)
    {
	  $keyname=trim($v['keyname']);	
          $keyname=strtolower($keyname);
	  if($keyname == $strfield)
		{
		  $tagid=$keyname;
		  break;
	    }
	}

    return $tagid;
}

public function validnum($keyword){
    //回复内容的数据类型：text，
	$keyword=strtolower($keyword);//转换为小写
    $msgType = "text";
    $this->dbsql();

	$keyword = strip_tags($keyword);
	if($keyword == ""){
		exit(0);
	}

		if($keyword)
		{
		  $bynow=time();
		  $insql= mysql_query("insert into `dede_addonweixin_norecord`(keyword,addtime) values('$keyword','$bynow');");
		  $recodesql = mysql_query("select `id` from `dede_addonweixin_norecord` order by id desc limit 0,1");
		  $recode = mysql_fetch_array($recodesql);   


		  $fkeyword=$this->cut_str($keyword,4); 
		  if($fkeyword=="1024")
		  {
			 $lkeyword= str_replace("1024", "", $keyword);

			 if($lkeyword !="")
			 {
				$select = mysql_query("SELECT * FROM `dede_addonweixin_keywords` arc where arc.keyword LIKE '%$lkeyword%'  order by arc.id desc limit 1");
				$group = mysql_fetch_array($select);	

				if(!$group)
				{
					$upinsql=mysql_query("update `dede_addonweixin_norecord` set `reply`=4 where `id`='$recode[id]'");
					$title="";
				}
				else
				{ 
					$title=$this->find2($lkeyword,$recode['id']);
				}

				return $title;	
				exit(0);
			 }

		  }
		  else
		  {

			$select = mysql_query("SELECT * FROM `dede_addonweixin_keywords` arc where arc.keyword LIKE '%$keyword%'  order by arc.id desc limit 1");
			$group = mysql_fetch_array($select);	

			if(!$group)
			{
				$title="";
			}
			else
			{ 
				$title=$this->find($keyword,$recode['id']);
			}

			return $title;
			exit(0);
		  }
		}
    }

public function dbsql()
	{
		require_once( dirname(__FILE__).'/weixindemoAdmin2013/topic/config/config.inc.php');
		$conn=mysql_connect($config['dbhost'],$config['dbuser'],$config['dbpass']);
		$flag=mysql_select_db($config['dbname'],$conn);
		mysql_query("set names utf8");
    }


public function cut_str($string,$sublen,$start=0,$code='UTF-8'){ 
		if($code == 'UTF-8' OR $code == 'utf-8'){
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
			preg_match_all($pa, $string, $t_string); 
			if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)).""; return join('', array_slice($t_string[0], $start, $sublen));
		}else{ 
			$start = $start*2; 
			$sublen = $sublen*2; 
			$strlen = strlen($string); 
			$tmpstr = ''; 
			for($i=0; $i< $strlen; $i++){ 
				if($i>=$start && $i< ($start+$sublen)){
					if(ord(substr($string, $i, 1))>129){
						$tmpstr.= substr($string, $i, 2);
					}else{
						$tmpstr.= substr($string, $i, 1);
					} 
				}
				if(ord(substr($string, $i, 1))>129) $i++;
			}
			return $tmpstr;
		}
	} 

public function wqmatch($keyword)
	{

		$searchsql = mysql_query("SELECT * FROM `dede_addonweixin_keywords` where keyword like '$keyword' limit 0,1");
		$getone = mysql_fetch_array($searchsql); 
		if(!$getone)
		{
		  $searchsql = mysql_query("SELECT * FROM `dede_addonweixin_keywords` where keyword like '%$keyword%' limit 0,1");
		  $getone = mysql_fetch_array($searchsql); 		
		}

	if($getone)
		{
		$sqlinfo = mysql_query("SELECT * FROM `dede_addonweixin` where aid=".$getone['aid']);
		$info = mysql_fetch_array($sqlinfo);

		$sql = mysql_query("SELECT * FROM `dede_arctype` where id=".$getone['typeid']);
		$typename = mysql_fetch_array($sql);

        $grouptitle = htmlspecialchars_decode($info['title']);

		$title_top ="您查询的结果为：";
		$title_top .="\n";
		$title_cen ="【".$typename['typename']."：".$grouptitle."】";
		$title_cen.="\n";
		$title = $info['zhaiyao'];
		$title = strip_tags($title);
		$title = preg_replace('/^\s+\r?\n/m',"\n", $title);
                $title = htmlspecialchars_decode($title);
		$title = str_replace("&nbsp;","",$title);
		$title = $this->cut_str($title,150);
		$srcurl="http://m.kuqin.com/detail.php?id=".$info[aid];
		$view1 = '【<a href="';
		$view2 = '">查看详情</a>】';
		$title =$title_top.$title_cen.$title.$view1.$srcurl.$view2."\n\n";   
		}
	 else
		{
	 	$title = ""; 
	    }

	  return $title;
	}


public function listmatch($keyword)
	{

		$addsql = "";
		$searchsql = mysql_query("SELECT * FROM `dede_addonweixin_keywords` where keyword like '$keyword' limit 0,1");
		$getone = mysql_fetch_array($searchsql);
		if($getone)
		{
		$getid = $getone['aid'];
		$addsql = " arc.aid !='$getid' and ";
		}
		else
		{
			$searchsql2 = mysql_query("SELECT * FROM `dede_addonweixin_keywords` where keyword like '%$keyword%' limit 0,1");
			$getone2 = mysql_fetch_array($searchsql2);	
			if($getone2)
			{
				$getid = $getone2['aid'];
				$addsql = " arc.aid !='$getid' and ";			
			}
		}


		$title_top = "";
		$title_cen1 = "";
		$title = "";
		$title1 = "";

		$select = mysql_query("SELECT DISTINCT aid FROM `dede_addonweixin_keywords` arc where $addsql arc.keyword LIKE '%$keyword%' order by arc.id desc limit 0,10 ");
		while ($row = mysql_fetch_array($select, MYSQL_BOTH)) {

			if($row)
			{

				$select1 = mysql_query("SELECT * FROM `dede_addonweixin` arc where arc.aid=".$row['aid']);
				$group1 = mysql_fetch_array($select1);	

				if($group1['aid']!="")
				{
				 $i++;
				 $sql = mysql_query("SELECT * FROM `dede_arctype` where id=".$group1['typeid']);
				 $typename = mysql_fetch_array($sql);

				 $group1title=trim($group1['title']);
				 $grouptitle1 = htmlspecialchars_decode($group1title);
				 $srcurl1="http://m.kuqin.com/detail.php?id=".$group1['aid'];
				 $view2 = "<a href='";
				 $view3 = "'>".$grouptitle1."</a>";
				 $grouptitle2 = $view2.$srcurl1.$view3;
				 $title_cen1 ="【".$i.".".$typename['typename']."：".str_replace("'",'"',$grouptitle2)."】";
                 $title_top = "其他查询结果为：\n";		
				 $title .=$title_cen1."\n\n";
				}
			}

		}
	  
	  $title1 = $title_top.$title;
	  return $title1;
	}


public function nofind($keyword,$recodeid)
	{

		$upinsql=mysql_query("update `dede_addonweixin_norecord` set `reply`=2 where `id`='$recodeid'");

		$sql = mysql_query("SELECT * FROM `tb_reply_news` arc where arc.id=1");
		$nofind = mysql_fetch_array($sql);
		$title=$nofind['nofind'];
	
		$title = str_replace("&nbsp;","",$title);
		$title = str_replace("<p>","",$title);
		$title = str_replace("</p>","",$title);		
		$title = preg_replace('/^\s+\r?\n/m',"\n", $title);
		$title = htmlspecialchars_decode($title);

	    return $title;
	}

public function find($keyword,$recodeid)
	{
		$upinsql=mysql_query("update `dede_addonweixin_norecord` set `reply`=1 where `id`='$recodeid'");

        $info = $this->wqmatch($keyword);
	    $listinfo = $this->listmatch($keyword);        
		
		$sql = mysql_query("SELECT * FROM `tb_reply_news` arc where arc.id=1");
		$find = mysql_fetch_array($sql);
		$stringfind=$find['find'];
		
		$stringfind = str_replace("&nbsp;","",$stringfind);
		$stringfind = str_replace("<p>","",$stringfind);
		$stringfind = str_replace("</p>","",$stringfind);
		$stringfind = preg_replace('/^\s+\r?\n/m',"\n", $stringfind);
		$stringfind = htmlspecialchars_decode($stringfind);

		$title =$info.$listinfo.$stringfind;
	    return $title;
	}

public function find2($s1,$recodeid)
	{
	    $upinsql= mysql_query("update `dede_addonweixin_norecord` set `reply`=1 where `id`='$recodeid'");

        $info = $this->wqmatch($s1);
	    $listinfo = $this->listmatch($s1);      
		
		$sql = mysql_query("SELECT * FROM `tb_reply_news` arc where arc.id=1");
		$find = mysql_fetch_array($sql);
		$stringfind=$find['find'];
	
		$stringfind = str_replace("&nbsp;","",$stringfind);
		$stringfind = str_replace("<p>","",$stringfind);
		$stringfind = str_replace("</p>","",$stringfind);		
		$stringfind = preg_replace('/^\s+\r?\n/m',"\n", $stringfind);
		$stringfind = htmlspecialchars_decode($stringfind);
		
		$title =$info.$listinfo.$stringfind;

	    return $title;
	}

}


$list=new getdatedemo();
$view=$list->keysinfo("酷勤");

print_r($view);
?>
