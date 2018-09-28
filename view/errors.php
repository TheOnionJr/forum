<?php  if (count($errors) > 0) : ?>
  <div class="error">
  	<?php echo "
  	  	<script type=\"text/javascript\">
  	  			window.confirm(\"" ?>
  		<?php foreach ($errors as $error) : ?>
  	  		<?php echo filter_var($error, FILTER_SANITIZE_STRING)?>
  	  	<?php endforeach ?>
  	  <?php echo "\")</script>" ?>
  	
  </div>
<?php  endif ?>