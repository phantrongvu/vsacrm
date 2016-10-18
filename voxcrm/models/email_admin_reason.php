<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/03/13
 * Time: 11:03 PM
 * To change this template use File | Settings | File Templates.
 */
class Email_admin_reason extends CI_Model
{
  const Error = '|1|';
  const TECHNICAL_SUPPORT = '|2|';
  const ADJUSTMENT_PACKAGE = '|3|';
  const ADJUSTMENT_U24 = '|4|';
  const NEW_CLIENT = '|5|';
  const REFERRAL_CLIENT = '|6|';
  const REPORT_PAYMENT_CREDIT_CARD = '|7|';
  const REPORT_PAYMENT_DIRECT_DEBIT = '|8|';
  const REMOVAL = '|9|';
  const REPORT_INCIDENT = '|10|';

  function email_admins($eid = null)
  {
    if (empty($eid))
    {
      return $this->db->get('email_admin')->result();
    }
    else
    {
      return $this->db->where('eid', $eid)->get('email_admin')->row();
    }
  }

  function save($data)
  {
    if(isset($data->eid))
    {
      $this->db->where('eid', $data->eid);
      $this->db->update('email_admin', $data);
      return $data->eid;
    }
    else
    {
      $this->db->insert('email_admin', $data);
      return $this->db->insert_id();
    }
  }

  function delete($eid)
  {
    $this->db->delete('email_admin', array('eid' => $eid));
  }

  function reason_options()
  {
    static $reasons;

    if(empty($reasons))
    {
      $ref = new ReflectionClass('Email_admin_reason');
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
