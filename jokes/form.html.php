<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/includes/html.inc.php'; ?>
<html lang = "en">
   <head>
      <meta charset = "utf-8">
      <title><?php htmlout($pageTitle); ?></title>
      <style type="text/css">
      textarea
      {
      	display:  block;
      	width: 100%;
      }</style>
  </head>
  <body>
  	<h1><?php htmlout($pageTitle);?></h1>
  	<form action = "?<?php htmlout($action); ?>" method = "post">
  		<div><!--传进text =<?php htmlout($text); ?> 的内容-->
  			<label for = "text">Type your joke here:</label>

  			<textarea id = "text" name = "text" rows = "4" cols = "40">
  				<?php htmlout($text); ?>
  			</textarea>
  		</div>
  		<div>
        <!--传递author = <?php htmlout($author['id']); ?> -->
  			<label for = "author">Author:</label>
  			<select name = "author" id = "author">
  				<option value = "">Select one</option>
  				<?php foreach ($authors as $author): ?>
  					<option value = "<?php htmlout($author['id']); ?>"
              <?php 
              //选择
              if($author['id'] == $authorid)
          					{
          						echo ' selected';
          					}?>>
              <?php htmlout($author['name']); ?>
            </option>
  				<?php endforeach; ?>
  			</select>
  		</div>
  		<fieldset>
  			<legend>Categories:</legend>
  			<?php foreach ($categories as $category): ?>
  			<div>
  				<label for = "category<?php htmlout($category['id']); ?>">
  					<input type = "checkbox" name = "categories[]" id = "category<?php htmlout($category['id']); ?>"
  					value = "<?php htmlout($category['id']); ?>"<?php
  					if($category['selected'])
  					{
  						echo ' checked';
  					}?>><?php htmlout($category['name']); ?>
            <!--可以选择多个分类，因此name = categories[],即用一个数组来存放value值-->
  				</label>
  			</div>
  		    <?php endforeach; ?>
  		</fieldset>
  	    <div>
  	    <input type = "hidden" name = "id" value = "<?php htmlout($id);?>">
  	    <input type = "submit" value = "<?php htmlout($button);?>">
  	    </div>
    </form>
  </body>
</html>