<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="/bootstrap/css/bootstrap-utilities.min.css"/>
  <link rel="stylesheet" href="/css/common.scss" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript" src="/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="/js/common.js"></script>

  <?php if (isset($header) && !empty($header)) : ?>
  <?php foreach ($header as $key=>$val) : 
    if ( $key == 'css') : 
      foreach($val as $css) : 
        if ( strpos( strtolower($css), 'http') !== false ) {
          echo '<link rel="stylesheet" href="'.$css.'">';
        } else echo '<link rel="stylesheet" href="/css'.$css.'">';
      endforeach;
    elseif ( $key == 'js') : 
      foreach ( $val as $js ) : 
        if ( strpos( strtolower($js), 'http') !== false ) {
          echo '<script type="text/javascript" src="'.$js.'"></script>';
        } else echo '<script type="text/javascript" src="/js'.$js.'"></script>';
      endforeach;
    endif;
  endforeach?>
  <?php endif ?>
  <title>Beautynetkorea B2B</title>
</head>
<?php if ( session()->isLoggedIn ) { ?>
<body class='<?=session()->currency['currencyUnit']?>'>
<?php } else echo "<body>"; ?>
<?php
  if ( session()->isLoggedIn ) {
    echo view('layout/nav');
  } else echo view('layout/nav2');
?>