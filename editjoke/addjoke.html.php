<!DOCTYPE html>
<html lang = "en">
  <head>
     <meta charest="utf-8";>
     <title>ADD joke</title>
     <style type="text/css">
     textarea
     {
     	display: block;
     	width: 70%;
     	}</style>
  </head>
  <body>
  	<form action="" method="post">
  		<div>
  			<label for = "joketext">Type your joke here:</label>
  			<textarea id ="joketext" name = "joketext" rows = "4" cols = “80”>
  			</textarea>
  		</div>
  		<div><input type = "submit" name = "action" value = "add">  <?php include 'logout.inc.html.php'; ?></div>

  	</form>
  </body>
 </html>