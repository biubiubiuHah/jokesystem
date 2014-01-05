<?php
if(get_magic_quotes_gpc())
{
	$process = array($_GET,&$_POST,&$_COOKIE,&$_REQUEST);
	while(list($key,$val) = each($process))
	{
		foreach($val as $k => $v)
		{
			unset($process[$key][$k]);
			if(is_array($v))
			{
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			}
			else
			{
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}
/*
* 措施：检测魔术引号是否在Web服务器上是否可用，可用就除掉它对提交的值所作出的修改
*（php 5.4以上已经关闭废弃掉魔术引号功能）
*
* 在用户进行输入时会引发SQL注入式攻击
*（用户输入一些不好的SQL代码，脚本会毫无保留的提交给MySQL服务器）
*
* 措施：魔术引号功能（检测“危险”字符，假如反斜杠），预防SQL注入式攻击。
*
* 导致的问题：只在某些情况下起作用，（不同站点字符编码及数据库服务器情况不同）
* 并且当提交的不是创建一条SQL查询时，反斜杠就成了麻烦
*
*
*根据php手册提供的代码，检测魔术引号是否在Web服务器上是否可用，如果可以，则去掉它对提交的值所做的修改
*这就是预处理功能
*/