<?php include $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
     <meta charset = "utf-8" >
     <title>Search Jokes</title>
  </head>
     <body>
        <h1>Search Jokes</h1>
        <!--检测到jokes[]里有内容，即joke表中收的到笑话-->
        <?php if(isset($jokes)): ?>
        <table border = "1">
        	<tr><th>Joke Text</th><th>Options</th></tr>
            <!--显示笑话-->
        	<?php foreach ($jokes as $joke) :?>
        	<tr>
        		<td><?php htmlout($joke['text']); ?></td>
        		<td>
        			<form action = "?" method = "post">
        				<div>
                            <!--传进了id,action值分别为joke的id，Edit,Delete-->
        					<input type = "hidden" name = "id" value = "<?php htmlout($joke['id']);?>" >
        					<input type = "submit" name = "action" value = "Edit">
        					<input type = "submit" name = "action" value = "Delete">
        				</div>
        			</form>
        		</td>
        	</tr>
        	<?php endforeach;?>
        </table>
    <?php endif; ?>
    <?php if(!isset($jokes)):  echo "there is no such joke in the database"; ?><?php endif ;?>
    <p><a href="?">New Search</a></p>
    <p><a href="../jokeback/">Return to JMS home</a></p>
    <?php include '../logout.inc.html.php'; ?>
    </body>
</html>