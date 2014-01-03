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
}//根据php手册提供的代码，检测魔术引号是否在Web服务器上是否可用，如果可以，则去掉它对提交的值所做的修改
//这就是预处理功能