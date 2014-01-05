 <?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/access.inc.php';

//检测是否单击了'Enrol'，单击则显示注册页面 
if(isset($_POST['action']) and $_POST['action'] == 'Enrol')
{
	include 'editer.html.php';
	exit();
}
//检测是否单击了‘注册’
if(isset($_POST['Enrolcommit']) )
{
 	include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	try
 	{
 		$password = md5($_POST['password'].'jokedb');
 		//插入用户的信息
 		$sql = 'INSERT INTO author SET name = :name,email = :email,password = :password';
 		$s = $pdo -> prepare($sql);
 		$s -> bindValue(':name',$_POST['name']);
 		$s -> bindValue(':email',$_POST['email']);
 		$s -> bindValue(':password',$password);
 		$s -> execute();
 		
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error to enrol a new editer'.$e->getMessage();
 		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 		exit();
 	}

 	//获取用户插入的id
    $authorid = $pdo -> lastInsertId();	
    
    //给予用户权限
 	try
 	{
 		$sql = 'INSERT INTO authorrole SET authorid = :authorid,roleid = "Content Editor"';
 		$s = $pdo -> prepare($sql);
 		$s -> bindValue(':authorid',$authorid);
 		$s ->execute();
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error to enrol a new editer as an Content Editor'.$e->getMessage();
 		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 		exit();
 	}
 	header('Location: .');
 	exit();
}

//用户未登陆，显示登陆表单
if(!userIsLoggedIn())
{
    include 'login.html.php';
    exit();
}

//检测用户是否单击‘写笑话’
if(isset($_POST['action']) and $_POST['action'] == 'add')
{
	include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

	//启动会话以选择哪个用户
	try
	{
		session_start();
		$sql = 'SELECT id FROM author WHERE email = :email';
		$s = $pdo -> prepare($sql);
		$s -> bindValue(':email',$_SESSION['email']);
		$s -> execute();
	}
	catch(PDOException $e)
	{
		$output = 'error to fetching the id from the author'.$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	$authorid = $s;
	try
	{
		$sql = 'INSERT INTO joke SET 
		     joketext = :joketext,
		     jokedate = CURDATE(),
		     authorid = :authorid,
		     zan = 0';
		$s = $pdo -> prepare($sql);
		$s -> bindValue(':joketext',$_POST['joketext']);
		$s -> bindValue(':authorid',$authorid);
		$s -> execute();
	}
	catch(PDOException $e)
	{
		$output = 'error to insert joketext to the joke.'.$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	header('Location: .');
	exit();
}

//显示添加笑话的表单
include 'addjoke.html.php';