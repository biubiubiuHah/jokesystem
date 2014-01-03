<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<!DOCTYPE html>
<html lang = "en">
  <head>
  	<meta charset = "utf-8">
    <title><?php htmlout($pageTitle);?></title>
  </head>
<body>
	<h2><?php htmlout($pageTitle);?> </h2>
	<form action = "?<?php htmlout($action); ?>" method = "post">
		<div>
			<label for = "name">Name :<input type = "text" name = "name"
				id = "name" value = "<?php htmlout($name); ?>"></label>
		</div>
		<div>
			<input type = "hidden" name = "id" value = "<?php htmlout($id); ?>">
			<input type = "submit" value = "<?php htmlout($button);?>" >
		</div>
	</form>
</body>
</html>

