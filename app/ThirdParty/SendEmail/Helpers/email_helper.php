<?php
use SendEmail\Models\EmailModel;

if ( !function_exists('get_mail_idx') ) {
  function get_mail_idx($key = 'register') {
    $emailModel = new EmailModel();

    $email = $emailModel->where('key', $key)->first();

    return $email['idx'];
  }
}