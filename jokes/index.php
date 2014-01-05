<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/magicquotes.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/access.inc.php';

 //用户未登陆，显示登陆表单
 if(!userIsLoggedIn())
 {
    include '../login.html.php';
    exit();
 }


 //用户登陆但缺乏所需的角色，显示一条相应的错误信息
 if(!userHasRole('Content Editor'))
 {
    $output = 'Only Content Editor may access this page.';
    include $_SERVER['DOCUMENT_ROOT'].'/admin/accessdentied.html.php';
    exit();
 }

//单击Add new joke 后执行的代码。此过程与Edit操作类似，只是没有显示相关的author及category信息
if(isset($_GET['add']))
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

//单击Add joke 后执行的代码
if(isset($_GET['addform']))
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

	//由from.html.php中传进categories[]数组，可以使一个笑话有多个类别
	if(isset($_POST['categories']))
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

//单击Search 后执行的代码
if(isset($_GET['action']) and $_GET['action'] == 'search')
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

		//一次性的把占位符中的值传入进mysql中
		$s->execute($placeholders);
	}
	catch(PDOException $e)
	{
		$output = "error fetching the jokes".$e->getMessage();
	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	    exit();
	}
	foreach ($s as $row) 
	{
		//把得到的结果放进jokes中，在jokes表单进行显示
		$jokes[] = array('id' => $row['id'],'text' => $row['joketext']);
	}

	include 'jokes.html.php';
	exit();
}

//再执行完Search命令后显示的结果，再单击Edit 后执行的代码
if(isset($_POST['action']) and $_POST['action'] == 'Edit')
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

	try
	{
		//取出joke表中的id,joketext,authorid信息
		$sql = 'SELECT id,joketext,authorid FROM joke WHERE id = :id';

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
		//取出作者，为后面代码备用
		$result = $pdo->query('SELECT id,name FROM author');
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
		//categoryid取出，jokeid = :id上
		$sql = 'SELECT categoryid FROM jokecategory WHERE jokeid = :id';
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
		//取出category里的id，name
		$result = $pdo->query('SELECT id,name FROM category');
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

//再点击Update Joke 后执行的代码。
if(isset($_GET['editform']))
{
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

	//若无选择author 则返回提醒选择作者
	if($_POST['author'] == '')
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

		//由select的author的id
		$s->bindValue(':authorid',$_POST['author']);  
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
		//删除jokecategory中jokeid与categoryid匹配的信息 :id为jokes.html.php中传递的值
		$sql = 'DELETE FROM jokecategory WHERE jokeid = :id';
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
			//重新插入jokecategory中的jokeid,categoryid,一个笑话，多个类别
			$sql = 'INSERT INTO jokecategory SET 
			jokeid = :jokeid,
			categoryid = :categoryid';
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

//删掉笑话：须删除jokecategory表中的jokeid与categoryid的信息
//joke表中相关的笑话信息（joketext，id，jokedate，authorid）
if(isset($_POST['action']) and $_POST['action'] == 'Delete')

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

include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';  
try
{
	//选择author表里的id,name
	$result = $pdo->query('SELECT id,name FROM author'); 
}
catch(PDOException $e)
{
	$output = "error fetching the author from database".$e->getMessage();
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	exit();
}
foreach($result as $row)
{
	//把数据放进authors[]数组
	$authors[] = array('id' => $row['id'],'name' => $row['name']);
}
try
{
	//选择category里的id,name
	$result = $pdo->query('SELECT id,name FROM category');
}
catch(PDOException $e)
{
	$output = "error fetching the category from database".$e->getMessage();
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	exit();
}
foreach($result as $row)
{
	//把数据放进categories[]数组里
	$categories[] = array('id' => $row['id'],'name' => $row['name']);
}

//表单模板显示Manage Jokes 的界面
include 'searchform.html.php';