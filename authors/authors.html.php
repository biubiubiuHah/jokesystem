<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
  	<meta charset = "utf-8">
  	<title>Manage Authors</title>
  </head>
  <body>
  	<h1>Manage Authors</h1>
  	<p><a href = "?add">Add new authors</a></p>
  	<ul>
  		<?php foreach($authors as $author): ?>
  		  <li>
  		  	<form action = "" method = "post">
  		  		<div>
  		  			<?php htmlout($author['name']); ?>
  		  			<input type = "hidden" name = "id" value = "<?php echo $author['id'];?>">
  		  			<input type = "submit" name = "action" value = "Edit">
  		  			<input type = "submit" name = "action" value = "Delete">
  		  		</div>
  		  	</form>
  		  </li>
  		<?php endforeach;?>
  	</ul>
    <p><a href = "../jokeback/">Return to JMS home</a></p>
    <?php include '../logout.inc.html.php'; ?>
  </body>
 </html>