<?php  if (count($errorsthread) > 0) : ?>
  <div class="errorsthread">
    <?php foreach ($errorsthread as $error) : ?>
      <p><?php echo $error ?></p>
    <?php endforeach ?>
  </div>
<?php  endif ?>