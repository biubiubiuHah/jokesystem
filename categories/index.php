 <?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/magicquotes.inc.php';

 require_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/access.inc.php';
 if(!userIsLoggedIn())//用户未登陆，显示登陆表单
 {
    include '../login.html.php';
    exit();
 }

 if(!userHasRole('Site Administrator '))//用户登陆但缺乏所需的角色，显示一条相应的错误信息
 {
    $output = 'Only Site Administrator  may access this page.';
    include $_SERVER['DOCUMENT_ROOT'].'/admin/accessdentied.html.php';
    exit();
 }
 
 if(isset($_GET['add']))//点击ADD new author 后的执行代码
 {
 	$pageTitle = 'New Category';
 	$action = 'addform';
 	$name = '';
 	$email = '';
 	$id ='';
 	$button = 'Add category';

 	include 'form.html.php';
 	exit();
 }
 if(isset($_GET['addform']))//点击 Add author 后，即提交表单后执行插入author
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	try
 	{
 		$sql = 'INSERT INTO category SET 
 		       name = :name';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':name',$_POST['name']);
 		$s->execute();       
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error adding submitted category'.$e->getMessage;
 	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
 	header('Location: .');
 	exit();
 }




 if (isset($_POST['action']) and $_POST['action'] == 'Edit') //点击Edit 后执行的代码，对author 进行修改
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

 	try
 	{
 		$sql = 'SELECT id,name FROM category WHERE id = :id';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':id',$_POST['id']);
 		$s->execute();
 	}
 	catch (PDOExecption $e)
 	{
 		$output = 'error fetching category details'.$e->getMessage;
 	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
 	$row = $s->fetch();
    
    $pageTitle = 'Edit Author';
    $action = 'editform';
    $name = $row['name'];
    $id = $row['id'];
    $button = 'Update category';

    include 'form.html.php';	
    exit();
 }
 if(isset($_GET['editform']))//点击 Update author 后，即提交表单后执行修改author
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	try
 	{
 		$sql = 'UPDATE category SET 
 		       name = :name
 		       WHERE id = :id';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':id',$_POST['id']);
 		$s->bindValue(':name',$_POST['name']);
 		$s->execute();       
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error updating submitted category'.$e->getMessage;
 	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
 	header('Location: .');
 	exit();
 }



 if(isset($_POST['action']) and $_POST['action'] == 'Delete')//点击Delete后执行的操作，即删除author，并且直接回到Manage author 的界面
 {
 	 include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	 //GET jokes belonging to author 
 	 try//在jokecategory表里删除categoryid 
	{
		$sql = 'DELETE FROM jokecategory WHERE categoryid = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch(PDOExecption $e)
	 {
	 	$output = 'error removing jokes from category.'.$e->getMessage;
	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	 	exit();
	 }

	 try//删除分类
	 {
	 	$sql = 'DELETE FROM category WHERE id = :id';
		$s = $pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	 }
	 catch(PDOExecption $e)
	 {
	 	$output = 'error deleteing category.'.$e->getMessage;
	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	 	exit();
	 }
	 header('Location: .');
	 exit();
}
 include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php'; //Manage author 的界面
 try
 {
    $result = $pdo->query('SELECT id,name FROM category');
 }
 catch(PDOException $e)
 {
    $output = 'error fetching categories from the database!';
   include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
    exit();
 }
 foreach($result as $row)
 {
    $categories[] = array('id' => $row['id'],'name' => $row['name']);
 }
 require 'categories.html.php';

