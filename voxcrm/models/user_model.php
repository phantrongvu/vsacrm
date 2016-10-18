<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
  function login($mail, $pass)
  {
    $this->load->helper('security');

    $q = $this
          ->db
          ->where('mail', $mail)
          ->where('pass', do_hash($pass))
          ->limit(1)
          ->get('users');

    if ($q->num_rows() > 0)
    {
      return $q->row();
    }

    return FALSE;
  }

  function staff_info($uid)
  {
    $q = $this
      ->db
      ->where('uid', $uid)
      ->limit(1)
      ->get('users');

    if($q->num_rows() > 0)
    {
      return $q->row();
    }

    return FALSE;
  }

  function student_info($sid)
  {
    $q = $this
      ->db
      ->where('sid', $sid)
      ->limit(1)
      ->get('students');

    if($q->num_rows() > 0)
    {
      return $q->row();
    }

    return FALSE;
  }

  function roles($uid)
  {
    $roles = array();
    $sql = "
      SELECT r.* FROM user_role ur
      INNER JOIN roles r ON r.rid = ur.rid
      WHERE ur.uid = ?
    ";

    $q = $this->db->query($sql, array($uid));

    if($q->num_rows() > 0)
    {
      foreach($q->result() as $row)
      {
        $roles[$row->rid] = $row->name;
      }
    }

    return $roles;
  }

  // takes a user ID and a role ID
  // checks if role ID matches user ID
  // returns true if true
  function has_role($uid, $rid)
  {
    $sql = "
      SELECT * FROM  user_role
      WHERE uid = ? AND rid = ?
      ";

    $q = $this->db->query($sql, array($uid, $rid));

    if($q->num_rows() > 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function prepare_empty_user()
  {
    return (object)array(
      'uid' => '',
      'mail' => '',
      'first_name' => '',
      'last_name' => '',
      'dob' => '',
      'street' => '',
      'additional' => '',
      'city' => '',
      'postcode' => '',
      'state' => '',
      'phone' => '',
      'mobile' => '',
      'description' => '',
      'roles' => array(),
      'rate' => 0,
      'availability' => 0,
    );
  }

  function save_staff($data)
  {
    $this->load->helper('security');

    if( ! empty($data->pass))
    {
      $data->pass = do_hash($data->pass);
    }

    if(isset($data->uid))
    {
      $this->db->where('uid', $data->uid);
      $this->db->update('users', $data);
      return $data->uid;
    }
    else
    {
      $data->created = time();
      $this->db->insert('users', $data);
      return $this->db->insert_id();
    }
  }

  function save_student($data)
  {
    if(isset($data->sid))
    {
      $this->db->where('sid', $data->sid);
      $this->db->update('students', $data);
      return $data->sid;
    }
    else
    {
      $data->created = time();
      $this->db->insert('students', $data);
      return $this->db->insert_id();
    }
  }

  function set_roles($uid, $rids = array())
  {
    $this->db->delete('user_role', array('uid' => $uid));

    foreach($rids as $rid)
    {
      $this->db->insert('user_role', array('uid' => $uid, 'rid' => $rid));
    }
  }

  function search($type = 'student', $q = '', $start = 0, $limit = 20)
  {
    $items = array();
    $q = $this->db->escape_like_str($q);
    switch($type)
    {
      case 'student':
        $query = $this->db->query("
          SELECT * FROM students
          WHERE mail LIKE '%%$q%%' OR first_name LIKE '%%$q%%' OR last_name LIKE '%%$q%%'
          ORDER BY first_name
          LIMIT $start, $limit
        ");

        $count = $this->db->query("
          SELECT COUNT(*) AS cnt FROM students
          WHERE mail LIKE '%%$q%%' OR first_name LIKE '%%$q%%' OR last_name LIKE '%%$q%%'
        ")->row()->cnt;
        break;

      case 'staff':
        $query = $this->db->query("
          SELECT * FROM users
          WHERE mail LIKE '%%$q%%' OR first_name LIKE '%%$q%%' OR last_name LIKE '%%$q%%'
          ORDER BY first_name
          LIMIT $start, $limit
        ");

        $count = $this->db->query("
          SELECT COUNT(*) AS cnt FROM users
          WHERE mail LIKE '%%$q%%' OR first_name LIKE '%%$q%%' OR last_name LIKE '%%$q%%'
        ")->row()->cnt;
        break;
    }

    if($query->num_rows > 0)
    {
      foreach($query->result() as $row)
      {
        $items[] = $row;
      }

      return array('items' => $items, 'count' => $count);
    }

    return array('items' => array(), 'count' => FALSE);
  }
  
  function check_email($q = '')
  {
	$query = $this->db->query("
	  SELECT sid FROM students
	  WHERE mail = '$q'
	  ORDER BY first_name
	  LIMIT 1
	");
	
    if($query->num_rows > 0)
    {
      return $query->row();
    }

    return false;
  }

  function check_email_2($q = '')
  {
	$query = $this->db->query("
	  SELECT uid FROM users
	  WHERE mail = '$q'
	  ORDER BY first_name
	  LIMIT 1
	");
	
    if($query->num_rows > 0)
    {
      return $query->row();
    }

    return false;
  }
  
  function delete($type = 'student', $id)
  {
    switch($type)
    {
      case 'student':
        $this->db->delete('students', array('sid' => $id));

        //delete events & schedules associated with this student
        $this->db->query("DELETE FROM schedules
         WHERE eid IN (SELECT eid FROM events WHERE student_id = ?)", array($id));
        $this->db->delete('events', array('student_id' => $id));

        break;

      case 'staff':
        // prevent delete admin
        if($id != 1)
        {
          $this->db->delete('users', array('uid' => $id));

          // clean up schedules and events
          $ret = $this->db->query("SELECT eid FROM schedules WHERE staff_id = ?", array($id))->row();
          if(!empty($ret)) {
            $this->db->delete('events', array('eid' => $ret->eid));
            $this->db->delete('schedules', array('eid' => $ret->eid));
          }
        }
        break;
    }
  }

  function list_options($roles = array('teacher'))
  {
    $items = array();
    $sql = "
      SELECT u.uid, u.first_name, u.last_name, u.mail
      FROM users u
      INNER JOIN user_role ur ON ur.uid = u.uid
      INNER JOIN roles r ON r.rid = ur.rid
      WHERE r.name IN ('" . implode("','", $roles)  . "')
      ORDER BY u.first_name
    ";
    $query = $this->db->query($sql)->result();
    foreach($query as $row)
    {
      $items[$row->uid] = "$row->first_name $row->last_name ($row->mail)";
    }

    return $items;
  }

  function display_name($user)
  {
    return $user->first_name . ' ' . $user->last_name . '(' . $user->mail . ')';
  }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
