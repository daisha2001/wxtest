<?php
header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__)."/include/lib_splitword_full.php");
class getdatedemo{

public function newsinfo($keyword){
	$keyword=strtolower($keyword);//转换为小写
    $this->dbsql(); 
    $keyword = strip_tags($keyword);
    if($keyword == ""){
            exit(0);
    }
    $krows=array();

    $keywordssql=mysql_query("select `aid`,`pipei` from `dede_addonweixin10` where `pipei` !='' and typeid='13'");
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
	  $sql=mysql_query("SELECT title,weixinpic,zhaiyao,weixinurl,pipei,shuxing FROM `dede_addonweixin10` arc where arc.aid in ($idstring) and arc.typeid='13' order by arc.flag desc,arc.aid desc limit 4");

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

	  $sql5=mysql_query("select * from (select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where $addsql  and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp1 union select * from (SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing,flag,tag FROM `dede_addonweixin10` arc where  FIND_IN_SET('$tagid', arc.tag)>0 and arc.shuxing like '单图文'  order by arc.flag desc,arc.aid desc) as temp2) as temp3 order by flag desc,aid desc limit 8");

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
		  $tagid=$v['id'];
		  break;
	    }
	}

    return $tagid;
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



}


?>
