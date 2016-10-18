<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('event_model');
  }

  private function _get_schedules($uid, $start, $end)
  {
    $sql = "
      SELECT e.*, e.status AS event_status, s.*, p.name AS product_name
      FROM schedules s
      INNER JOIN events e ON s.eid = e.eid
      INNER JOIN products p ON e.product_id = p.pid
      WHERE s.staff_id = ? AND s.start BETWEEN ? AND ? AND e.status != ?
      ORDER BY s.start ASC
    ";

    return $this->db->query($sql, array(
      $uid, $start, $end, Event_model::STATUS_DELETED
    ))->result();
  }

  function timesheet($uid, $start, $end)
  {
    return $this->_get_schedules($uid, $start, $end);
  }

  function teacher_performance($uid, $start, $end)
  {
    $sql = "
      SELECT e.*, e.status AS event_status, s.*, p.name AS product_name, p.lessons
      FROM schedules s
      INNER JOIN events e ON s.eid = e.eid
      INNER JOIN products p ON e.product_id = p.pid
      WHERE s.staff_id = ? AND s.start BETWEEN ? AND ? AND e.status != ?
      ORDER BY s.start ASC
    ";

    return $this->db->query($sql, array(
      $uid, $start, $end, Event_model::STATUS_DELETED
    ))->result();
  }

  function email_admin($uid, $start, $end)
  {
    $sql = "
      SELECT e.*
      FROM email_admin e
      WHERE e.uid = ? AND e.created BETWEEN ? AND ?
      ORDER BY e.created ASC
    ";

    return $this->db->query($sql, array(
      $uid, $start, $end
    ))->result();
  }

  function sale_performance($start, $end, $key = 'pending')
  {
    switch($key)
    {
      case 'pending':
        $sql = "
        SELECT count(*) AS cnt
        FROM events e
        WHERE e.created BETWEEN ? AND ? AND e.status = ?
        ";

        return $this->db->query($sql, array(
          $start, $end, Event_model::STATUS_UNPAID
        ))->result();
        break;

      case 'won':
        $sql = "
        SELECT count(*) AS cnt
        FROM events e
        WHERE e.created BETWEEN ? AND ? AND e.status IN (?)
        ";

        return $this->db->query($sql, array(
          $start, $end, implode(',', array(Event_model::STATUS_ACTIVE, Event_model::STATUS_INACTIVE))
        ))->result();
        break;

      case 'lost':
        $sql = "
        SELECT e.*
        FROM events e
        WHERE e.created BETWEEN ? AND ? AND e.status = ?
        ORDER BY e.created ASC
        ";

        return $this->db->query($sql, array(
          $start, $end, Event_model::STATUS_DELETED
        ))->result();
        break;
    }
  }

  function sale_forecast($start, $end)
  {
    $sql = "
        SELECT SUM(p.price) AS total
        FROM events e
        INNER JOIN products p ON e.product_id = p.pid
        WHERE e.created BETWEEN ? AND ? AND e.status IN (?)
        ";

    return $this->db->query($sql, array(
      $start, $end, implode(',', array(Event_model::STATUS_ACTIVE, Event_model::STATUS_INACTIVE))
    ))->result();
  }
}

/* End of file report_model.php */
/* Location: ./application/models/report_model.php */
