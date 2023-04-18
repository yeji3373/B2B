<?php
namespace FtpFile\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Files\File;

class FtpFileController extends Controller {
  protected $config; 

  public function __construct() {
    $this->config = config('FtpFile');
  }

  public function fileUpload($FILES, $name = null, $ftpPath = null) {
    // if ( is_null($path)  ) 
    $ftpPath = '/b2b/documents/register/certification/';    
    $success = false;
    $msg = null;
    $fileName = null;

    $ftpConn = ftp_ssl_connect($this->config->ftpUrl, $this->config->ftpPort);

    if ( $ftpConn !== false ) {
      if ( @ftp_login($ftpConn, $this->config->ftpUser, $this->config->ftpPass) ) {
        $ext = strtolower(pathinfo($FILES['name'])['extension']);
        
        if ( !is_null($name)) {
          $fileName = "{$name}.{$ext}";
        } else $fileName = data('Ymd').'_'.time().'.'.$ext;
        
        $localFile = $FILES['tmp_name'];
        $serverFile = $ftpPath.$fileName;

        if ( !file_exists($ftpPath.$fileName) ) {
          ftp_pasv($ftpConn, true);

          if ( @ftp_put($ftpConn, $serverFile, $localFile, FTP_BINARY) ) {
            $success = true;
          } else {
            print_r($FILES);
            echo "<Br/>server file {$serverFile} local File {$FILES['tmp_name']}";
            $msg = "put failed";
          }
        } else {
          $msg = "{$fileName} is already exists";
        }
      } else {
        $msg = "login error";
      }
    } else {
      $msg = "connect error";
    }
    ftp_close($ftpConn);
    echo "<Br/>msg {$msg}<br/>";
    return ['res' => $success, 'msg' => $msg];
  }

  // public function ftpPath($idx = null) {
  //   // $path = $
  //   return $path;
  // }
}