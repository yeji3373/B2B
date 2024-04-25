<?php
$validation = \Config\Services::validation();
$msg = null;

if ( $validation->hasError($col) ) {
  $msg = $validation->getError($col);
} else {
  $msg = $default;
}

if ( !is_null($msg) ) {
echo "<span class='guide-msg color-red'>
        <i></i>
        {$msg}
      </span>";
}
?>