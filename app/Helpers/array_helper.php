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