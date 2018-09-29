<?php  if (count($errors) > 0) : ?>										<!-- If error -->
  <div class="error">
  	<!-- Start javascript -->
  	<?php echo "
  	<script type=\"text/javascript\">
  	  	window.confirm(\"" ?>
  			<?php foreach ($errors as $error) : ?> 						<!-- Print all errors -->
  	  		<?php echo filter_var($error, FILTER_SANITIZE_STRING)?>		<!-- Actuall printing and filtering -->
  	  		<?php endforeach ?>											<!-- This needs some improvement -->
  	<?php echo "\")</script>" ?>
  	<!-- End javascript -->
  </div>
<?php  endif ?>

