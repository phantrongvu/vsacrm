<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 27/12/12
 * Time: 10:34 AM
 * To change this template use File | Settings | File Templates.
 */

if ( ! function_exists('check_date'))
{
  function check_date($date)
  {
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
      if(checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4)))
        return true;
      else
        return false;
    } else {
      return false;
    }
  }
}

if ( ! function_exists('date_to_system'))
{
  function date_to_system($date)
  {
	date_default_timezone_set('Australia/Victoria');
    $dates = explode('/', $date);
    if (count($dates) === 3)
	{
      $dates[1] = sprintf("%02s", $dates[1]);
      $dates[0] = sprintf("%02s", $dates[0]);
      return date('Y-m-d', strtotime($dates[2] . $dates[1] . $dates[0]));
	}
    return FALSE;
  }
}

if ( ! function_exists('date_to_display'))
{
  function date_to_display($date)
  {
    if($date == '0000-00-00')
    {
      return '';
    }
    return date('d/m/Y', strtotime($date));
  }
}

if ( ! function_exists('state_options'))
{
  function state_options()
  {
    return array(
      '' => '...',
      'VIC' => 'VIC',
      'NSW' => 'NSW',
      'QLD' => 'QLD',
      'SA' => 'SA',
      'WA' => 'WA',
      'TAS' => 'TAS',
      'NT' => 'NT',
      'ACT' => 'ACT',
    );
  }
}

if ( ! function_exists('product_recurring'))
{
  function product_recurring()
  {
    return array(
      1 => '1 week',
      2 => '2 weeks'
    );
  }
}

if ( ! function_exists('time_options'))
{
  function time_options($op = 'from')
  {
    $options = array();
    $CI =& get_instance();
    for($i = $CI->config->item('studio_start_time'); $i < $CI->config->item('studio_end_time'); $i+=0.5)
    {
      $options[strval($i)] = time_value($i);
    }

    if($op == 'to') {
      $options[strval($i)] = time_value($i);
    }
    return $options;
  }
}

if ( ! function_exists('time_value'))
{
  function time_value($i)
  {
    $output = '';
    $str_i_arr = explode('.', strval($i));
    $am_pm = 'AM';

    if($str_i_arr[0] > 12)
    {
      $str_i_arr[0] = $str_i_arr[0] - 12;
      $am_pm = 'PM';
    }
    else if($str_i_arr[0] == 12)
    {
      $str_i_arr[0] = 12;
      $am_pm = 'PM';
    }

    if(count($str_i_arr) > 1)
    {
      $output .= str_pad($str_i_arr[0], 2, '0', STR_PAD_LEFT) . ':30 ' . $am_pm;
    }
    else
    {
      $output .= str_pad($str_i_arr[0], 2, '0', STR_PAD_LEFT) . ':00 ' . $am_pm;
    }

    return $output;
  }
}

if ( ! function_exists('time_option'))
{
  function time_option($timestamp)
  {
    if(intval(date('i', $timestamp)) / 60 == 0.5) {
      return intval(date('G', $timestamp)) + 0.5;
    }
    return intval(date('G', $timestamp));
  }
}

if ( ! function_exists('my_send_mail'))
{
  function my_send_mail($options = array())
  {
    if(empty($options['to']) || empty($options['message']))
    {
      show_error('Failed to send mail.');
    }

    if (empty($options['subject']))
    {
      $options['subject'] = 'VOXCRM Mail';
    }
    if (empty($options['template']))
    {
      $options['template'] = 'default';
    }
    if (empty($options['message']))
    {
      $options['message'] = 'Test VOXCRM Mail';
    }

    $CI =& get_instance();
    $mail = $CI->config->item('voxcrm_mail');
    $CI->load->library( 'email' );
    $CI->email->from( $mail['address'], $mail['name'] );

    // prevent sending real mail on dev environment
    if($_SERVER['HTTP_HOST'] == 'vsacrm2.dev') {
      $options['to'] = 'phantrongvu.teacher.1@gmail.com';
    }
    $CI->email->to( $options['to'] );

    $CI->email->subject( $options['subject'] );
    $CI->email->message( $CI->load->view( 'emails/' . $options['template'], array('message' => $options['message']), true ) );
    $CI->email->set_mailtype('html');
    $CI->email->send();
  }
}

if ( ! function_exists('my_schedule_compare')) {
  function my_schedule_compare($schedule_1, $schedule_2)
  {
    if ($schedule_1->start == $schedule_2->start) {
      return 0;
    }

    return ($schedule_1->start < $schedule_2->start) ? -1 : 1;
  }
}

if ( ! function_exists('set_to_day_of_week'))
{
  function shift_to_day_of_week($date, $n = 1)
  {
    if(date('N', $date) == $n)
    {
      return $date;
    }
    else if(date('N', $date) > $n)
    {
      return strtotime('-' . (date('N', $date) - $n) . ' days', $date);
    }
    else
    {
      return strtotime(abs(date('N', $date) - $n) - 7 . ' days', $date);
    }
  }
}
