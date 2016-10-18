<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/03/13
 * Time: 11:03 PM
 * To change this template use File | Settings | File Templates.
 */
class Reason_model extends CI_Model
{
  const FORGOT = '|1|';
  const SICK = '|2|';
  const WORK_COMMITMENTS = '|3|';
  const SCHOOL = '|4|';
  const TRANSPORT = '|5|';
  const PRIVATE_REASONS = '|6|';
  const DOUBLE_LESSON = '|8|';
  const HOLIDAYS = '|9|';
  const PUBLIC_HOLIDAY = '|13|';
  const FORTNIGHTLY_LESSON = '|10|';
  const ATTENDANCE = '|11|';
  const MANAGER_APPROVAL = '|12|';
  const OTHER = '|7|';

  function reason_options()
  {
    static $reasons;

    if(empty($reasons))
    {
      $ref = new ReflectionClass('Reason_model');
      $reasons = array_flip($ref->getConstants());
    }

    return $reasons;
  }

  function reason_value($key) {
    static $reasons;

    if(empty($reasons))
    {
      $reasons = $this->reason_options();
    }

    if(isset($reasons[$key]))
    {
      return str_replace('_', ' ', $reasons[$key]);
    }

    return $key;
  }
}
