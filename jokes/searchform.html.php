<?php include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
     <meta charset = "utf-8" >
     <title>Manage Jokes</title>
  </head>
     <body>
        <h1>Manage Jokes</h1>
        	<p><a href = "?add">Add new joke</a></p><!--单击Add new joke，传入add到$_POST[]数组里-->
        	<form action = "" method = "get">
        		<p>View jokes satisfying the following criteria:</p>
        		<div>
        			<label for = "author">By author:</label>
        			<select name = "author" id = "author">
        				<option value = ""> Any author</option>
        				<?php foreach ($authors as $author):?> 
        				<option value = "<?php htmlout($author['id']);?>"><?php htmlout($author['name']) ?></option>
        				<?php endforeach;?>
        			</select>
        			</div>
        			<div>
        				<label for = "category">By category:</label>
        			<select name = "category" id = "category">
        				<option value = ""> Any category</option>
        				<?php foreach ($categories as $category):?> 
        				<option value = "<?php htmlout($category['id']);?>"><?php htmlout($category['name']) ?></option>
        				<?php endforeach;?>
        			</select>
        			</div>
        			<div>
        				<label for = "text">Containing text:</label>
        				<input type = "text" name = "text" id = "text"><!--把text传进$_GET[]数组里,值也是text-->
        			</div>
        			<div>
        				<input type = "hidden" name = "action" value = "search"><!--把action传进数组$_GET[]里，值是search-->
        				<input type = "submit" value = "search"></div>
        		</form>
        		<p><a href = "../jokeback/"><input type = "submit" value = "Return to JMS home"></a><p>
                    <?php include '../logout.inc.html.php'; ?>
        	</body>
 </html>