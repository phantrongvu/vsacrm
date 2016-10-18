<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 27/12/12
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */

// make sure the function doesn't already exist
// You don’t want to mistakenly override an existing function
if ( ! function_exists('page_not_found'))
{
  function page_not_found()
  {
    redirect('errors/error_404');
  }
}

if ( ! function_exists('page_access_denied'))
{
  function page_access_denied()
  {
    redirect('errors/error_403');
  }
}

if ( ! function_exists('_error_log'))
{
  function _error_log($obj, $label = null)
  {
    error_log($label . ' - ' . print_r($obj, TRUE));
  }
}
