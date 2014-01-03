<?php
try
{
 $pdo = new PDO('mysql:host=localhost;dbname=jokedb','root','20130608qQq');
 $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 $pdo->exec('SET NAMES "utf8"');
}
catch (PDOException $e)
{
 $output = 'Unable to connect to the database server'.$e->getMessage();
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/output.html.php';
 exit();
}
