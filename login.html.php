<!-- 登陆表单-->
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
  	<meta charset = "utf-8">
  	<title>Log In</title>
  </head>
  <body>
  	<h1>Log In</h1>
  	<p>Please log in to view page that you requested.</p>
  	<?php if(isset($loginError)): ?><!--如果检测到全局变量loginError 即输出错误信息-->
     	<p><?php htmlout($loginError); ?></p>
     <?php endif; ?>
     <form action = "" method = "post"><!--接受一个action = login，还有email= ? password =  ?-->
     	<div>
     		<label for = "email">Email: <input type = "text", name = "email" id = "email"></label>
     	</div>
     	<div>
     		<label for = "password">Password: <input type = "password" name = "password" id = "password"></label>
     	</div>
     	<div>
     		<input type = "hidden" name = "action" value = "login">
     		<input type = "submit" value = "Log in">
     	</div>
     </form>
     <p><a href="/admin/jokeback/">Return to JMS home</a></p>
  </body>
</html>