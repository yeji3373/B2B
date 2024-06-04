<?php

if ( !function_exists('array_equal') ) {
  function array_equal($arr1, $arr2) {
    return (
      is_array($arr1) 
      && is_array($arr2)
      && count($arr1) == count($arr2)
      && array_diff($arr1, $arr2) == array_diff($arr2, $arr1)
    );
  }
}

//https://gist.github.com/Snaver/7921096
if ( !function_exists('add_prefix') ) {
  function add_prefix($array, $prefix) {
    return array_combine(
      array_map(
        function($k, $prefix) {
          return $prefix.$k;
        }, array_keys($array),
        array_fill(0, count($array), $prefix)
      ), 
      $array
    );
  }
}
//https://gist.github.com/Snaver/7921096
if ( !function_exists('remove_prefix') ) {
  function remove_prefix($array, $prefix)
  {			
    return array_combine(
      array_map(
        function($k,$prefix){
          return preg_replace("/^$prefix/", '', $k);
        },
        array_keys($array),
        array_fill(0 , count($array) , $prefix)
      ),
      $array
    );
  }
}