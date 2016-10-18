<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/03/13
 * Time: 11:03 PM
 * To change this template use File | Settings | File Templates.
 */
class Delete_reason_model extends CI_Model
{
  const BOOKING_ERROR = '|1|';
  const ADJUSTMENT_PACKAGE = '|2|';
  const ADJUSTMENT_U24 = '|3|';
  const NON_PAYMENT = '|4|';
  const OVERDUE_ACCOUNT = '|5|';
  const ATTENDANCE = '|6|';
  const WORK = '|7|';
  const SCHOOL = '|8|';
  const SICK = '|9|';
  const TRANSPORT = '|10|';
  const HOLIDAYS = '|11|';
  const PUBLIC_HOLIDAY = '|14|';
  const COMPLAINT = '|15|';
  const PRIVATE_REASONS = '|12|';
  const ADMIN = '|13|';

  function reason_options()
  {
    static $reasons;

    if(empty($reasons))
    {
      $ref = new ReflectionClass('Delete_reason_model');
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
