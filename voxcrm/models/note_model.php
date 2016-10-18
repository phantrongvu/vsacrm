<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Note_model extends CI_Model
{
  function notes($nid = null)
  {
    if (empty($nid))
    {
      return $this->db->get('notes')->result();
    }
    else
    {
      return $this->db->where('nid', $nid)->get('notes')->row();
    }
  }

  function search($sid = 0, $start = 0, $limit = 20)
  {
    $count = $this->db->query("
        SELECT COUNT(*) AS cnt FROM notes
        WHERE sid = ?
      ", array($sid))->row()->cnt;

    $sql = "SELECT * FROM `notes`
WHERE `sid` = $sid
ORDER BY `created` DESC LIMIT $start, $limit";

    $result = $this->db->query($sql)->result();

    return array(
      'result' => $result,
      'count' => $count
    );
  }

  function prepare_empty_note()
  {
    return (object)array(
      'nid' => '',
      'sid' => '',
      'uid' => '',
      'title' => '',
      'body' => '',
      'created' => '',
    );
  }

  function save($data)
  {
    if(isset($data->nid))
    {
      $this->db->where('nid', $data->nid);
      $this->db->update('notes', $data);
      return $data->nid;
    }
    else
    {
      $this->db->insert('notes', $data);
      return $this->db->insert_id();
    }
  }

  function delete($nid)
  {
    if($nid != 1)
    {
      $this->db->delete('notes', array('nid' => $nid));
    }
  }

  function note_history($sid, $limit = 5)
  {
    return $this->db->where('sid', $sid)->order_by('nid', 'desc')->get('notes', $limit, 0)->result();
  }
}

/* End of file note_model.php */
/* Location: ./application/models/note_model.php */
