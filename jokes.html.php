<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin//includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
    <meta charset = "utf-8">
    <title>Joke</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style type="text/css">
  body {
	background-color: #CCC;
}
  </style>
  </head>
  <body>
  
  <hr align="center" width="70%">   
  <table width="70%" border="1" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <th colspan="2"><strong>Joke ~welcome to you to come here!</strong></th>
    </tr>
    <?php foreach($jokes as $joke): ?>
    <tr>
      <td><?php htmlout($joke['text']); ?></td>
      <td>By:<a href="mailto:<?php htmlout($joke['email']); ?>"><?php htmlout($joke['name']); ?></a></td>
      <td><form action ="?" method = 'post'>
        <input type = "hidden" name = "id" value = "<?php htmlout($joke['id']); ?>">
        <input type = "hidden" name = "zan1" value = "<?php htmlout($joke['zan'])?>">
        <input type = "submit" name = "zan" value = "赞" >+<?php htmlout($joke['zan'])?></form></td>
    </tr>
  <?php endforeach; ?>
  </table>
  </body>
</html>