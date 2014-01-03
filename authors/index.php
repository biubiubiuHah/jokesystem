 <?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/magicquotes.inc.php';

 require_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/access.inc.php';
 if(!userIsLoggedIn())//用户未登陆，显示登陆表单
 {
    include '../login.html.php';
    exit();
 }

 if(!userHasRole('Account Administrator'))//用户登陆但缺乏所需的角色，显示一条相应的错误信息
 {
    $output = 'Only Account Administrator may access this page.';
    include $_SERVER['DOCUMENT_ROOT'].'/admin/accessdentied.html.php';
    exit();
 }


 if(isset($_GET['add']))//点击ADD new author 后的执行代码
 {
    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	$pageTitle = 'New Author';
 	$action = 'addform';
 	$name = '';
 	$email = '';
 	$id ='';
 	$button = 'Add author';

    try
    {
        $result = $pdo->query('SELECT id,description FROM role');
    }
    catch(PDOException $e)
    {
        $output = 'error fetching list of roles';
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
    }
    foreach ($result as $row) 
    {
        $roles[] = array('id' => $row['id'],'description' => $row['description'],'selected' => FALSE);
    }

 	include 'form.html.php';
 	exit();
 }
 if(isset($_GET['addform']))//点击 Add author 后，即提交表单后执行插入author
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	try
 	{
 		$sql = 'INSERT INTO author SET 
 		       name = :name, email = :email';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':name',$_POST['name']);
 		$s->bindValue(':email',$_POST['email']);
 		$s->execute();       
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error adding submitted author'.$e->getMessage;
 	    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
    $authorid = $pdo->lastInsertId();
    if($_POST['password'] != '')
    {
        $password = md5($_POST['password'].'jokedb');//对密码进行加密处理
        try
        {
            $sql = 'UPDATE author SET 
                 password = :password
                 WHERE id = :id';
             $s = $pdo -> prepare($sql);
             $s -> bindValue(':password',$password);
             $s -> bindValue(':id',$authorid);
             $s ->execute();
        }
        catch(PDOException $e)
        {
            $output = 'error setting author password';
            include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
            exit();
        }
        if(isset($_POST['roles']))
        {
            foreach ($_POST['roles'] as $role) 
            {
                try
                {
                    $sql = 'INSERT INTO authorrole SET 
                    authorid = :authorid,
                    roleid = :roleid';
                    $s = $pdo -> prepare($sql);
                    $s -> bindValue(':authorid',$authorid);
                    $s -> bindValue(':roleid',$role);
                    $s -> execute();
                }
                catch(PDOException $e)
                {
                    $output = 'error assign selected role to author.';
                    include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
                    exit();
                }
            }
        }

    }
 	header('Location: .');
 	exit();
 }




 if (isset($_POST['action']) and $_POST['action'] == 'Edit') //点击Edit 后执行的代码，对author 进行修改
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

 	try
 	{
 		$sql = 'SELECT id,name,email FROM author WHERE id = :id';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':id',$_POST['id']);
 		$s->execute();
 	}
 	catch (PDOExecption $e)
 	{
 		$output = 'error fetching author details'.$e->getMessage;
 	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
 	$row = $s->fetch();
    
    $pageTitle = 'Edit Author';
    $action = 'editform';
    $name = $row['name'];
    $email = $row['email'];
    $id = $row['id'];
    $button = 'Update author';
    //得到作者的所属用户权限
    try
    {
        $sql = 'SELECT roleid FROM authorrole WHERE authorid = :id';
        $s = $pdo -> prepare($sql);
        $s -> bindValue(':id',$id);
        $s ->execute();
    }
    catch(PDOException $e)
    {
        $output = 'Error fetching list of assigned roles.'.$e -> getMessage();
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
    }
    $selectedRoles = array();
    foreach($s as $row)
    {
        $selectedRoles[] = $row['roleid'];
    }

    //建立用户权限的信息
    try
    {
        $result = $pdo -> query('SELECT id,description FROM role');
    }
    catch(PDOException $e)
    {
        $output = 'Error fetching list of roles.'.$e -> getMessage();
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
    }
    foreach($result as $row)
    {
        $roles[] = array(
            'id' => $row['id'],
            'description' => $row['description'],
            'selected' => in_array($row['id'], $selectedRoles));
    }

    include 'form.html.php';
    exit();
 }
 if(isset($_GET['editform']))//点击 Update author 后，即提交表单后执行修改author
 {
 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
 	try
 	{
 		$sql = 'UPDATE author SET 
 		       name = :name,
 		       email = :email
 		       WHERE id = :id';
 		$s = $pdo->prepare($sql);
 		$s->bindValue(':id',$_POST['id']);
 		$s->bindValue(':name',$_POST['name']);
 		$s->bindValue(':email',$_POST['email']);
 		$s->execute();       
 	}
 	catch(PDOException $e)
 	{
 		$output = 'error updating submitted author'.$e->getMessage;
 	 	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
 	 	exit();
 	}
    if($_POST['password']!= '') //更新密码
    {
        $password = md5($_POST['password'].'jokedb');
        try
        {
            $sql = 'UPDATE author SET password = :password WHERE id = :id';
            $s = $pdo -> prepare($sql);
            $s -> bindValue(':password',$password);
            $s -> bindValue(':id',$_POST['id']);
            $s -> execute();
        }
        catch(PDOException $e)
        {
            $output = 'Error fetching list of roles.'.$e -> getMessage();
            include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
            exit();
        }
    }
    try//删除作者原先的用户权限信息
    {
        $sql = 'DELETE FROM authorrole WHERE authorid = :id';
        $s = $pdo -> prepare($sql);
        $s -> bindValue(':id',$_POST['id']);
        $s -> execute();
    }
    catch(PDOException $e)
    {
        $output = 'error removing obsolete author role entries';
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
    }

    if(isset($_POST['roles'])) //录入作者的用户权限信息
    {
        foreach($_POST['roles'] as $role)
        {
            try
            {
                $sql = 'INSERT INTO authorrole SET authorid = :authorid,
                roleid = :roleid';
                $s = $pdo -> prepare($sql);
                $s -> bindValue(':authorid',$_POST['id']);
                $s -> bindValue(':roleid',$role);
                $s -> execute();
            }
             catch(PDOException $e)
            {
                 $output = 'error assigning seletcted role to author';
                 include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
                 exit();
            }
        }
       
    }
 	header('Location: .');
 	exit();
 }
if(isset($_POST['action']) and $_POST['action'] == 'Delete')
//点击Delete后执行的操作，即删除author，并且直接回到Manage author 的界面
 {
     include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';

     //删除作者的用户权限信息
     try
     {
        $sql = 'DELETE FROM authorrole WHERE authorid = :id';
        $s = $pdo -> prepare($sql);
        $s -> bindValue(':id',$_POST['id']);
        $s -> execute();
     }
     catch(PDOExecption $e)
     {
        $output = 'error removing author from roles'.$e->getMessage;
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
     }
     //GET jokes belonging to author 
     try
     {
        $sql = 'SELECT id FROM joke WHERE authorid = :id';
        $s = $pdo->prepare($sql);
        $s->bindValue(':id',$_POST['id']);
        $s->execute();/*为甚么要这样？直接用author = $_POST['id'] 进行查询不就好了？
        原因：直接用author = $_POST['id'] 的话，那么$_POST['id']的内容就完全由用户控制，这会导致
        SQL注入式攻击（用户输入一些不好的SQL代码，脚本会毫无保留的提交给MySQL服务器）
        措施：魔术引号功能（检测“危险”字符，假如反斜杠），预防SQL注入式攻击。
        导致的问题：只在某些情况下起作用，（不同站点字符编码及数据库服务器情况不同）并且当提交的不是创建一条SQL查询时，反斜杠就成了麻烦
        措施：检测魔术引号是否在Web服务器上是否可用，可用就除掉它对提交的值所作出的修改（php 5.4以上已经关闭废弃掉魔术引号功能）*/
     }
     catch(PDOExecption $e)
     {
        $output = 'error getting list of jokes to delete'.$e->getMessage;
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
     }

     $result = $s->fetchALL();//新语句：使用：

     try//删除笑话目录中的笑话与分类的对应关系
     {
        $sql = 'DELETE FROM jokecategory WHERE jokeid = :id';//?????????
        $s = $pdo->prepare($sql);

        //foreach joke
        foreach ($result as $row) 
        {
            $jokeID = $row['id'];
            $s->bindValue(':id',$jokeID);
            $s->execute();
        }

     }
      catch(PDOExecption $e)
     {
        $output = 'error deleting category entries for joke'.$e->getMessage;
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
     }


     try//删掉属于作者的joke
     {
        $sql = 'DELETE FROM joke WHERE authorid = :id';
        $s = $pdo->prepare($sql);
        $s->bindValue(':id',$_POST['id']);
        $s->execute();
     }
      catch(PDOExecption $e)
     {
        $output = 'error delete jokes for author'.$e->getMessage;
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
     }


     try//删掉作者
     {
        $sql = 'DELETE FROM author WHERE id = :id';
        $s = $pdo->prepare($sql);
        $s->bindValue(':id', $_POST['id']);
        $s->execute();
     }
      catch(PDOExecption $e)
     {
        $output = 'error deleting author.'.$e->getMessage;
        include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
        exit();
     }

     header('Location: .');
     exit();

 }

include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php'; //Manage author 的界面
 try
 {
    $result = $pdo->query('SELECT id,name FROM author');
 }
 catch(PDOException $e)
 {
    $output = 'error fetching authors from the database!';
   include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
    exit();
 }
 foreach($result as $row)
 {
    $authors[] = array('id' => $row['id'],'name' => $row['name']);
 }
 include 'authors.html.php';
 



 
