<?php
namespace Instagram\Controllers;

use CodeIgniter\Controller;

class InstagramController extends Controller {
    public function __construct() {
        
    }

    public function index() {
        $result = $this->instagram();
        // print_r($result);
        return json_encode($result);
    }

    public function instagram() {
        $post = array (
            'fields' => 'id,media_type,media_url,permalink,thumbnail_url,username,caption',
            'access_token' => 'IGQWROTk9naHdfekQ1SkRrYUZAhcFkwVFBZAQTVuYWJIQmNYOFhLOHdRSG1VWVI1UlUyRjQ0R3dXRERFMmd6a2xTd1k3cTlLdG55WHhqai1ldUtGXzNJejRMR0RBQUhZASTVPVlJEVTBfQ19KQQZDZD'
        );
        $url = "https://graph.instagram.com/6606476526146984/media?".http_build_query($post);
        try {
            $curl_connection = curl_init($url);
            curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl_connection);
            curl_close($curl_connection);
        } catch(Exception $e) {
            return $e->getMessage();
        }
          
        $data = json_decode($result, true);
        $image_array= array();
          
        foreach ($data['data'] as $key => $row) {
            $code = $row['id'];  $username = $row['username'];
            $type = $row['media_type'];
            $link = $row['permalink'];
            $thum = ($type === 'VIDEO') ? $row['thumbnail_url'] : $row['media_url'];
            // $text = $row['caption'];
            array_push($image_array, array('username'=>$username, 'link'=>$link, 'thum'=>$thum));
        }

        return $image_array;
    }
}