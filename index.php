<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/db.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/admin//includes/html.inc.php';

if(isset($_POST['zan']))
{
	$num = $_POST['zan1'];
	$num += 1;
	try
	{
		$sql = 'UPDATE joke SET zan = :zan WHERE id = :id';
		$s = $pdo -> prepare($sql);
		$s -> bindValue(':zan',$num);
		$s -> bindValue(':id',$_POST['id']);
		$s -> execute();
	}
	catch(PDOException $e)
	{
		$output = 'error to zan this joke';
		include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
		exit();
	}
	header('Location: .');
	exit();
}

try
{
	$sql = 'SELECT joke.id,joketext,name,email,zan FROM joke INNER JOIN author ON authorid = author.id ';
	$result = $pdo -> query($sql);
}
catch(PDOException $e)
{
	$output = "error fetching jokes".$e -> getMessage();
	include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/output.html.php';
	exit();
}
foreach($result as $row)
{
	$jokes[] = array('id' => $row['id'],'text' => $row['joketext'],'name' => $row['name'],
		'email' => $row['email'],'zan' => $row['zan']);
}



include 'jokes.html.php';