<?php
header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__)."/include/lib_splitword_full.php");
class getdatedemo1{

public function keysinfo($keyword){
    $conn=$this->dbsql(); 
    $keyword = strip_tags($keyword);
    if($keyword == ""){
            exit(0);
    }

    $keyword = iconv("UTF-8","GBK", $keyword);

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

	  $sql5=mysql_query("SELECT aid,title,weixinpic,zhaiyao,weixinurl,pipei,shuxing FROM `dede_addonweixin10` arc where $addsql order by arc.aid desc limit 4");

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
    unset($conn);
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



public function dbsql()
	{
		require_once( dirname(__FILE__).'/weixindemoAdmin2013/topic/config/config.inc.php');
		$conn=mysql_connect($config['dbhost'],$config['dbuser'],$config['dbpass']);
		$flag=mysql_select_db($config['dbname'],$conn);
		mysql_query("set names utf8");
    }


}


?>
