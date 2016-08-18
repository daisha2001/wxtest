<?php
/**
 * @version        $Id: tags.php 1 2010-06-30 11:43:09Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once (dirname(__FILE__) . "/include/common.inc.php");
require_once (DEDEINC . "/arc.taglist.class.php");
$typeid=trim($typeid);
$tag =trim($tag);
$type=trim($type);
if($tag)
{
   $sql="select * from `tb_customtags` where `id`='$tag'";
   $row = $dsql->GetOne($sql);
   if(!$row)
   {
		header('HTTP/1.1 404 Not Found');
		exit();
   }
}

if($typeid)
{
   $sql2="select * from `dede_arctype` where `id`='$typeid' and `reid`='14'";
   $row2 = $dsql->GetOne($sql2);
   if(!$row2)
   {
		header('HTTP/1.1 404 Not Found');
		exit();
   }
}

if($tag && $typeid)
{

   $sql3="select * from `tb_customtags` where `id` like '$tag' and `catid`='$typeid'";
   $row3 = $dsql->GetOne($sql3);

   if(!$row3)
   {
		header('HTTP/1.1 404 Not Found');
		exit();
   }
}

if($type=="hot" && $tag =='' && $typeid)
{
	if($typeid)
	{
	   $sql2="select * from `dede_arctype` where `id`='$typeid' and `reid`='14'";
	   $row2 = $dsql->GetOne($sql2);
	   if(!$row2)
	   {
			header('HTTP/1.1 404 Not Found');
			exit();
	   }
	}

	$dlist = new TagList($tag,$typeid,$row2['rmtemplist']);
	$dlist->Display();
	exit();
}

if($tag !='' && $typeid)
{
	if($typeid)
	{
	   $sql2="select * from `dede_arctype` where `id`='$typeid' and `reid`='14'";
	   $row2 = $dsql->GetOne($sql2);
	}
    
	$dlist = new TagList($tag,$typeid,$row2['tagtemplist']);
	$dlist->Display();
	exit();
}
if($tag =='' && $typeid)
{
	if($typeid)
	{
	   $sql2="select * from `dede_arctype` where `id`='$typeid' and `reid`='14'";
	   $row2 = $dsql->GetOne($sql2);
	}

	$dlist = new TagList($tag,$typeid,$row2['taglisttemplist']);
	$dlist->Display();
	exit();
}


exit();