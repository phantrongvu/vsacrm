<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Studio_model extends CI_Model
{
  function studios($sid = null)
  {
    if (empty($sid))
    {
      return $this->db->get('studios')->result();
    }
    else
    {
      return $this->db->where('sid', $sid)->get('studios')->row();
    }
  }

  function prepare_empty_studio()
  {
    return (object)array(
      'sid' => '',
      'name' => '',
      'street' => '',
      'additional' => '',
      'city' => '',
      'postcode' => '',
      'state' => '',
      'phone' => '',
      'fax' => '',
      'description' => '',
    );
  }

  function save($data)
  {
    if(isset($data->sid))
    {
      $this->db->where('sid', $data->sid);
      $this->db->update('studios', $data);
      return $data->sid;
    }
    else
    {
      $this->db->insert('studios', $data);
      return $this->db->insert_id();
    }
  }

  function delete($sid)
  {
    if($sid != 1)
    {
      $this->db->delete('studios', array('sid' => $sid));

      // clean up schedules and events
      $ret = $this->db->query("SELECT eid FROM schedules WHERE studio_id = ?", array($sid))->row();
      if(!empty($ret)) {
        $this->db->delete('events', array('eid' => $ret->eid));
        $this->db->delete('schedules', array('eid' => $ret->eid));
      }
    }
  }

  function list_options($id = TRUE)
  {
    $items = array();
    $query = $this->db->query('SELECT s.sid, s.name FROM studios s ORDER BY s.name')->result();
    foreach($query as $row)
    {
      if($id)
      {
        $items[$row->sid] = $row->name;
      }
      else
      {
        $items[$row->name] = $row->name;
      }
    }

    return $items;
  }
}

/* End of file studio_model.php */
/* Location: ./application/models/studio_model.php */
