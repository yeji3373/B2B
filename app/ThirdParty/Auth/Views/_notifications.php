<?php if (session()->has('success')) : ?>
  <div class="notification success">
    <?= session('success') ?>
  </div>
<?php endif ?>

<?php if (session()->has('error')) : ?>
  <div class="notification error">
    <?= session('error') ?>
  </div>
<?php endif ?>

<?php if (session()->has('errors')) : ?>
  <ul class="notification error">
  <?php foreach (session('errors') as $error) : ?>
    <li><?= $error ?></li>
  <?php endforeach ?>
  </ul>
<?php endif ?>

<?php if ( session()->has('validation') ) : ?>
  <div class='notification error'>
  <?php if ( session('validation')->hasError('buyerName') ) :   
    echo session('validation')->getError('buyerName');
  endif; ?>
  </div>
<?php endif ?>