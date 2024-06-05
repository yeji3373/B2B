<?php

use Config\Services;
use SendEmail\Models\SMTPModel;
use Auth\Models\UserModel;

if (! function_exists('send_activation_email'))
{
    /**
    * Builds an account activation HTML email from views and sends it.
    */
    function send_activation_email($to, $activateHash)
    {
    	$htmlMessage = view('Auth\Views\emails\header');
    	$htmlMessage .= view('Auth\Views\emails\activation', ['hash' => $activateHash]);
    	$htmlMessage .= view('Auth\Views\emails\footer');
      
      send_email($htmlMessage, lang('Auth.registration'), $to);
    }
}

if (! function_exists('send_confirmation_email'))
{
    /**
    * Builds an email confirmation HTML email from views and sends it.
    */
    function send_confirmation_email($to, $activateHash)
    {
        $htmlMessage = view('Auth\Views\emails\header');
        $htmlMessage .= view('Auth\Views\emails\confirmation', ['hash' => $activateHash]);
        $htmlMessage .= view('Auth\Views\emails\footer');

        send_email($htmlMessage, lang('Auth.confirmEmail'), $to);
    }
}


if (! function_exists('send_notification_email'))
{
    /**
    * Builds a notification HTML email about email address change from views and sends it.
    */
    function send_notification_email($to)
    {
        $htmlMessage = view('Auth\Views\emails\header');
        $htmlMessage .= view('Auth\Views\emails\notification');
        $htmlMessage .= view('Auth\Views\emails\footer');

        send_email($htmlMessage, lang('Auth.emailUpdateRequest'), $to);
    }
}


if (! function_exists('send_password_reset_email'))
{
  /**
  * Builds a password reset HTML email from views and sends it.
  */
  function send_password_reset_email($to, $resetHash)
  {
      $htmlMessage = view('Auth\Views\emails\header');
      $htmlMessage .= view('Auth\Views\emails\reset', ['hash' => $resetHash]);
      $htmlMessage .= view('Auth\Views\emails\footer');

      send_email($htmlMessage, lang('Auth.passwordResetRequest'), $to);
  }
}

if ( ! function_exists('send_email') ) {
  function send_email($message, $subject, $to, $from = array()) {
    $email = \Config\Services::email();
    $smtp = new SMTPModel();
    $smtpInfo = $smtp->first();

    $email->initialize([
      'protocol' => 'smtp',
      'mailType' => 'html',
      'SMTPHost' => $smtpInfo['smtp_host'],
      'SMTPUser' => $smtpInfo['smtp_user'],
      'SMTPPass' => $smtpInfo['smtp_pwd']
    ]);
    
    $email->setFrom($smtpInfo['smtp_user'], 'BeautynetKorea Co.,');
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage($message);

    return $email->send();
  }
}

if ( ! function_exists('current_user') ) 
{
  function current_user() {
    if ( !session()->isLoggedIn ) {
      return redirect()->to('/');
    // } else {
    //   $userModel = new UserModel();
    //   return $userModel->getUserIndex(session()->userData['email']);
    }
  }
}