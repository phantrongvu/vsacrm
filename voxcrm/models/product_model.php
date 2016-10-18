<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model
{
  function products($pid = null)
  {
    if (empty($pid))
    {
      return $this->db->get('products')->result();
    }
    else
    {
      return $this->db->where('pid', $pid)->get('products')->row();
    }
  }

  function prepare_empty_product()
  {
    return (object)array(
      'pid' => '',
      'name' => '',
      'lessons' => '',
      'price' => '',
      'description' => '',
      'in_active_event' => false,
      'recurring' => 1
    );
  }

  function save($data)
  {
    if(isset($data->pid))
    {
      $this->db->where('pid', $data->pid);
      $this->db->update('products', $data);
      return $data->pid;
    }
    else
    {
      $this->db->insert('products', $data);
      return $this->db->insert_id();
    }
  }

  function delete($pid)
  {
    $this->db->delete('products', array('pid' => $pid));
    //delete events & schedules associated with this product
    $this->db->query("DELETE FROM schedules
    WHERE eid IN (SELECT eid FROM events WHERE product_id = ?)", array($pid));
    $this->db->delete('events', array('product_id' => $pid));
  }

  function list_options()
  {
    $items = array();
    $query = $this->db->query('SELECT p.pid, p.name FROM products p ORDER BY p.name')->result();
    foreach($query as $row)
    {
      $items[$row->pid] = $row->name;
    }

    return $items;
  }

  function single_lesson()
  {
    $items = array();
    $query = $this->db->query('SELECT p.pid, p.name FROM products p WHERE p.pid = 17')->result();
    foreach($query as $row)
    {
      $items[$row->pid] = $row->name;
    }
    
    return $items;
  }
}

/* End of file product_model.php */
/* Location: ./application/models/product_model.php */
