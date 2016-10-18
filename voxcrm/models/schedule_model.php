<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule_model extends CI_Model
{
  const STATUS_PENDING = 1;
  const STATUS_CANCELED_OVER_24 = 2;
  const STATUS_CANCELED_IN_24 = 3;
  const STATUS_PASSED = 4;
  const STATUS_DELETED = 5;
  const STATUS_PAID_PENDING = 6;
  const STATUS_UNPAID_PASSED = 7;
  const STATUS_PAID_GREEN = 8;
  
  function schedules($cid = null)
  {
    if (empty($cid))
    {
      return $this->db->get('schedules')->result();
    }
    else
    {
      return $this->db->where('cid', $cid)->get('schedules')->row();
    }
  }

  function status_options()
  {
    static $statuses;

    if(empty($statuses))
    {
      $ref = new ReflectionClass('Schedule_model');
      $statuses = $ref->getConstants();
    }

    return $statuses;
  }

  function event_schedules($eid)
  {
    return $this->db->where('eid', $eid)->get('schedules')->result();
  }

  function query($options)
  {
    switch($options['filter'])
    {
      case 'student':
        break;

      case 'teacher':
        $sql = "
          SELECT DISTINCT s.* FROM schedules s
          WHERE s.staff_id = ?
          AND s.start > {$options['start']} AND s.end < {$options['end']} AND s.status != " . Schedule_model::STATUS_DELETED;

        return $this->db->query($sql, array($options['ids']['teacher_id']))->result();
        break;

      case 'studio':
        $sql = "
          SELECT DISTINCT s.* FROM schedules s
          WHERE s.studio_id = ?
          AND start > {$options['start']} AND end < {$options['end']} AND s.status != " . Schedule_model::STATUS_DELETED;

        return $this->db->query($sql, array($options['ids']['studio_id']))->result();
        break;

      case 'studio_teacher':
        $sql = "
          SELECT DISTINCT s.* FROM schedules s
          WHERE s.studio_id = ? AND s.staff_id = ?
          AND s.start > {$options['start']} AND s.end < {$options['end']} AND s.status != " . Schedule_model::STATUS_DELETED;

        return $this->db->query($sql, array(
          $options['ids']['studio_id'],
          $options['ids']['teacher_id'])
        )->result();
        break;

      case '*':
        return $this->db->where("start > {$options['start']} AND end < {$options['end']} AND status != " . Schedule_model::STATUS_DELETED)->get('schedules')->result();
        break;
    }
  }

  function prepare_empty_schedule()
  {
    return (object)array(
      'cid' => '',
      'eid' => '',
      'staff_id' => '',
      'studio_id' => '',
      'sequence' => '',
      'start' => '',
      'end' => '',
      'status' => self::STATUS_PENDING,
      'description' => '',
    );
  }

  function save($data)
  {
    if(isset($data->cid))
    {
      $this->db->where('cid', $data->cid);
      $this->db->update('schedules', $data);
      return $data->cid;
    }
    else
    {
      $this->db->insert('schedules', $data);
      return $this->db->insert_id();
    }
  }

  function delete($cid)
  {
    $this->db->delete('schedules', array('cid' => $cid));
  }

  function status($key)
  {
    switch($key)
    {
      case self::STATUS_PENDING:
        return 'Pending (Unpaid)';
      case self::STATUS_CANCELED_IN_24:
        return 'Cancelled in 24 hours';
      case self::STATUS_CANCELED_OVER_24:
        return 'Cancelled over 24 hours';
      case self::STATUS_PASSED;
        return 'Passed (Paid)';
      case self::STATUS_DELETED:
        return 'Deleted';
      case self::STATUS_PAID_PENDING:
        return 'Pending (Paid)';
      case self::STATUS_UNPAID_PASSED:
        return 'Passed (Unpaid)';
      case self::STATUS_PAID_GREEN:
		return 'Pending (Paid)';
    }
  }

  /**
   * Check to see if schedule is valid
   * a valid schedule must not be:
   * - taught by same teacher AND
   * - within start/end range
   * - and pending
   * @param $schedule
   * @return bool
   */
  function is_valid($schedule)
  {
    $sql = "
SELECT 1 FROM schedules s
WHERE s.staff_id = {$schedule->staff_id} AND
((s.start < {$schedule->start} AND s.end > {$schedule->start}) OR
(s.start < {$schedule->end} AND s.end > {$schedule->end}) OR
(s.start = {$schedule->start} AND s.end = {$schedule->end}))
AND s.status IN (" . implode(', ', array(Schedule_model::STATUS_PENDING)) . ")
    ";

    $result = $this->db->query($sql)->result();
    return empty($result);
  }
  
  
			function is_valid_2($schedule)
			{
				$sql = trim("
					SELECT e.name As name, 
							CONCAT(u.first_name, ' ', u.last_name) As staff,
							s.sequence As sequence, 
							s.start As start, 
							s.end As end 
							FROM schedules As s, events As e, users As u
					WHERE s.eid=e.eid AND s.staff_id=u.uid AND s.staff_id = {$schedule->staff_id} AND
					((s.start >= {$schedule->start} AND s.end <= {$schedule->end}) OR
					(s.start < {$schedule->start} AND s.end > {$schedule->end}) OR
					(s.start >= {$schedule->start} AND {$schedule->end} > s.start AND {$schedule->end} < s.end) OR
					(s.start < {$schedule->start} AND s.end > {$schedule->start} AND {$schedule->end} >= s.end)) AND 
					s.status NOT IN (2,3,5)
				"); 
				
				$result = $this->db->query($sql)->result();
				
				return $result;				
			}

	
  function rework_schedules_sequence($new_schedule, $eid)
  {
    // get schedules
    $schedules = $this->db->query("SELECT * FROM schedules WHERE eid = ? AND sequence != 0", array($eid))->result();
    $schedules[] = $new_schedule;

    // delete all other schedules that not cancelled
    $this->db->query("DELETE FROM schedules WHERE eid = ? AND sequence != 0", array($eid));

    // sort schedules
    usort($schedules, 'my_schedule_compare');
    foreach($schedules as $i => $schedule)
    {
      unset($schedule->cid);
      $schedule->sequence = $i + 1;
      $this->save($schedule);
    }
  }
}

/* End of file schedule_model.php */
/* Location: ./application/models/schedule_model.php */
