<?php
function userIsLoggedIn()
{
	//检测是否有一个$_POST['action'] == login ,即用户是否提交了表单,没有则显示登陆页面
	if(isset($_POST['action']) and $_POST['action'] == 'login') 
	{

		//若用户无录入信息便提交，则提醒再次输入
		if(!isset($_POST['email']) or $_POST['email'] == '' or !isset($_POST['password']) or $_POST['password'] == '')
		{
			$GLOBALS['loginError'] = 'Please fill in both fields';
			return FALSE;
		}

		//密码经过加密处理，加密盐
		$password = md5($_POST['password'].'jokedb'); 

		//假如检测到数据库中的author表中有改用户信息，则会话建立，存储用户信息
		//否则启动会话，并且清空用户信息
		if(databaseContainAuthors($_POST['email'],$password)) 
		{
			session_start();
			$_SESSION['loggedIn'] = TRUE;
			$_SESSION['email'] = $_POST['email'];
			$_SESSION['password'] = $password;
			return TRUE;
		}
		else
		{
			session_start();
			unset($_SESSION['loggedIn']);
			unset($_SESSION['email']);
			unset($_SESSION['password']);
			$GLOBALS['loginError'] = 'The specified email address or password weas incorrect.';
			return FALSE;
		}
	}

	//检测到用户单击logout则，启动会话，清空会话内用户信息
	if(isset($_POST['action']) and $_POST['action'] == 'logout')
	{
		    session_start();
			unset($_SESSION['loggedIn']);
			unset($_SESSION['email']);
			unset($_SESSION['password']);
			header('Location: ' . $_POST['goto']);
			exit();
	}
	session_start();

	//检测是否有会话变量isset($_SESSION['loggedIn']，真则检查是否和数据库中的author表中的用户信息是否相同
	if(isset($_SESSION['loggedIn'])) 
	{
		return databaseContainAuthors($_SESSION['email'],$_SESSION['password']);
	}
}

function databaseContainAuthors($email,$password)
{
	include 'db.inc.php';

	//选择author表，并返回数值，即有用户信息时，$s > 0；
	try
	{
		$sql = 'SELECT COUNT(*) FROM author WHERE email = :email AND password = :password';
		$s = $pdo->prepare($sql);
		$s->bindValue(':email',$email);
		$s->bindValue(':password',$password);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = 'error searching for author.';
		include 'output.html.php';
		exit();
	}

	$row = $s->fetch();

	if($row[0] >0)
	{
		return TRUE;
	}
	else 
	{
		return FALSE;
	}
}

//此函数：检测该用户的对应的用户权限
function userHasRole($role) 
{
	include 'db.inc.php';

	try
	{
		$sql = "SELECT COUNT(*) FROM author
			INNER JOIN authorrole ON author.id = authorid
			INNER JOIN role ON roleid = role.id
			WHERE email = :email AND roleid = :roleId";
			$s = $pdo->prepare($sql);
			$s->bindValue(':email',$_SESSION['email']);
			$s->bindValue(':roleId',$role);
			$s->execute();
	}
	catch(PDOException $e)
	{
		$output = 'error searching for author roles.';
		include 'output.html.php';
		exit();
	}

	$row = $s->fetch();
	if($row[0] > 0)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}