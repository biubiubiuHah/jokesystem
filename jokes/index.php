<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/magicquotes.inc.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/access.inc.php';
 if(!userIsLoggedIn())//用户未登陆，显示登陆表单
 {
    include '../login.html.php';
    exit();
 }

 if(!userHasRole('Content Editor'))//用户登陆但缺乏所需的角色，显示一条相应的错误信息
 {
    $output = 'Only Content Editor may access this page.';
    include $_SERVER['DOCUMENT_ROOT'].'/admin/accessdentied.html.php';
    exit();
 }

if(isset($_GET['add']))//单击Add new joke 后执行的代码。此过程与Edit操作类似，只是没有显示相关的author及category信息
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
	$pageTitle = 'New Joke';
	$action = 'addform';//传进一个action 
	$text = '';
	$authorid = '';
	$id = '';
	$button = 'Add joke';
	try
	{
		$result = $pdo->query('SELECT id,name FROM author');
	}
	catch(PDOException $e)
	{
		$output = "error fetching list of author".$e->getMessage();
	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	    exit();
	}
	foreach ($result as $row) 
	{
		$authors[] = array('id' => $row['id'],'name' => $row['name']);
	}

	try
	{
		$result = $pdo->query('SELECT id,name FROM category');
	}
	catch(PDOException $e)
	{
		$output = "error fetching the category".$e->getMessage();
	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	    exit();
	}
	foreach ($result as $row) 
	{
		$categories[] = array('id' => $row['id'],'name' => $row['name'],'selected' => FALSE);
	}
	include 'form.html.php';
	exit();
}
if(isset($_GET['addform']))//单击Add joke 后执行的代码
{	
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
    
    if($_POST['author'] == '')
    {
    	$output = 'you must choose a author for this joke.Click &lsquo;back&rsquo; and try again.';
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
    	exit();
    }

    try
    {
    	$sql = 'INSERT INTO joke SET 
    	joketext = :joketext,
    	jokedate = CURDATE(),
    	authorid = :authorid ';
    	$s = $pdo->prepare($sql);
    	$s->bindValue(':joketext',$_POST['text']);
    	$s->bindValue(':authorid',$_POST['author']);
    	$s->execute();
    }
    catch(PDOException $e)
	{
		$output = "error adding submmited joke".$e->getMessage();
	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	    exit();
	}
	$jokeid = $pdo->lastInsertId();
	if(isset($_POST['categories']))//由from.html.php中传进categories[]数组，可以使一个笑话有多个类别
	{
		try
		{
			$sql = 'INSERT INTO jokecategory SET
			jokeid = :jokeid,
			categoryid = :categoryid';
			$s = $pdo->prepare($sql);
			foreach($_POST['categories'] as $categoryid)
			{
				$s->bindValue(':jokeid',$jokeid);
				$s->bindValue(':categoryid',$categoryid);
				$s->execute();
			}

		}
		catch(PDOException $e)
		{
			$output = "error inserting joke into selected categories".$e->getMessage();
		    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		    exit();
		}
	}
	header('Location: .');
	exit();
}


if(isset($_GET['action']) and $_GET['action'] == 'search')//单击Search 后执行的代码
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

	$select = 'SELECT id,joketext';
	$from = ' FROM joke';
	$where = ' WHERE TRUE';
	$placeholders = array();

	if($_GET['author'] != '')
	{
		$where .= " AND authorid = :authorid";
		$placeholders[':authorid'] = $_GET['author'];
	}

	if($_GET['category'] != '')
	{
		$from .= ' INNER JOIN jokecategory ON id = jokeid';
		$where .= " AND categoryid = :categoryid";
		$placeholders[':categoryid'] = $_GET['category'];
	}
	if($_GET['text'] != '')
	{
		$where .= " AND joketext LIKE :joketext";
		$placeholders[':joketext'] = '%' . $_GET['text'] . '%';
	}
	try
	{
		$sql = $select . $from . $where;
		$s = $pdo->prepare($sql);
		$s->execute($placeholders);//一次性的把占位符中的值传入进mysql中
	}
	catch(PDOException $e)
	{
		$output = "error fetching the jokes".$e->getMessage();
	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	    exit();
	}
	foreach ($s as $row) 
	{
		$jokes[] = array('id' => $row['id'],'text' => $row['joketext']);//把得到的结果放进jokes中，在jokes表单进行显示
	}

	include 'jokes.html.php';
	exit();
}


if(isset($_POST['action']) and $_POST['action'] == 'Edit')//再执行完Search命令后显示的结果，再单击Edit 后执行的代码
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

	try
	{
		$sql = 'SELECT id,joketext,authorid FROM joke WHERE id = :id';//取出joke表中的id,joketext,authorid信息
		//:id信息为jokes.html.php 中所传输的joke的id 
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error fetching the joketext from the joke".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	$row = $s->fetch();

	$pageTitle = 'Edit Joke';
	$action = 'editform' ;
	$text = $row['joketext'];
	$authorid = $row['authorid'];
    $id = $row['id'];
    $button = 'Update Joke';

    try
	{
		$result = $pdo->query('SELECT id,name FROM author');//取出作者，为后面代码备用
	}
	catch(PDOException $e)
	{
		$output = "error fetching the author from database".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	foreach($result as $row)
	{
		$authors[] = array('id' => $row['id'],'name' => $row['name']);
	}

	try
	{
		$sql = 'SELECT categoryid FROM jokecategory WHERE jokeid = :id';//categoryid取出，jokeid = :id上
		$s = $pdo->prepare($sql);
		$s -> bindValue(':id',$id);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error fetching the categoryid from jokecategory".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	foreach ($s as $row) 
	{
		$selectCategories[] = $row['categoryid'];
	}


	try
	{
		$result = $pdo->query('SELECT id,name FROM category');//取出category里的id，name
	}
	catch(PDOException $e)
	{
		$output = "error fetching the category from database".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	foreach($result as $row)
	{
		$categories[] = array('id' => $row['id'],'name' => $row['name'],'selected' =>in_array($row['id'], $selectCategories));
	}
	include 'form.html.php';
	exit();
}
if(isset($_GET['editform']))//再点击Update Joke 后执行的代码。
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
	if($_POST['author'] == '')//若无选择author 则返回提醒选择作者
	{
		$error = 'you must choose an author for this joke.Click &lsquo;back&rsquo; and try again.';
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	try
	{
		$sql = 'UPDATE joke SET 
		joketext = :joketext,
		authorid = :authorid
		WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->bindValue(':joketext',$_POST['text']);
		$s->bindValue(':authorid',$_POST['author']);  //由select的author的id
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error updating submitted joke".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id';//删除jokecategory中jokeid与categoryid匹配的信息 :id为jokes.html.php中传递的值
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error removing obsolete joke category entries".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}

	if(isset($_POST['categories']))
	{
		try
		{
			$sql = 'INSERT INTO jokecategory SET 
			jokeid = :jokeid,
			categoryid = :categoryid';//重新插入jokecategory中的jokeid,categoryid,一个笑话，多个类别
			$s = $pdo->prepare($sql);

			foreach ($_POST['categories'] as $categoryid) 
			{
				$s->bindValue(':jokeid',$_POST['id']);
				$s->bindValue(':categoryid',$categoryid);
				$s->execute();
			}
		}
		catch(PDOException $e)
		{
			$output = "error inserting joke into selected categories.".$e->getMessage();
			include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
			exit();
		}

	}
	header('Location: .');
	exit();
}


if(isset($_POST['action']) and $_POST['action'] == 'Delete')//删掉笑话：须删除jokecategory表中的jokeid与categoryid的信息
//joke表中相关的笑话信息（joketext，id，jokedate，authorid）
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php'; 
	//delete category assignments for the joke 
	try
	{
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id ';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error removing joke from categories".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	try
	{
		$sql = 'DELETE FROM joke WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$output = "error delete joke".$e->getMessage();
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	header('Location: .');
	exit();

}

include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';  //连接数据库
try
{
	$result = $pdo->query('SELECT id,name FROM author'); //选择author表里的id,name
}
catch(PDOException $e)
{
	$output = "error fetching the author from database".$e->getMessage();
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	exit();
}
foreach($result as $row)
{
	$authors[] = array('id' => $row['id'],'name' => $row['name']);//把数据放进authors[]数组
}
try
{
	$result = $pdo->query('SELECT id,name FROM category');//选择category里的id,name
}
catch(PDOException $e)
{
	$output = "error fetching the category from database".$e->getMessage();
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	exit();
}
foreach($result as $row)
{
	$categories[] = array('id' => $row['id'],'name' => $row['name']);//把数据放进categories[]数组里
}
include 'searchform.html.php';//表单模板显示Manage Jokes 的界面