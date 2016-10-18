<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_model extends CI_Model
{
  function check_permission($uid, $perm)
  {
    $this->create_permission($perm);

    // root user always has full permissions
    if($uid == 1)
    {
      return TRUE;
    }

    $sql = "
      SELECT 1 FROM permissions p
      INNER JOIN role_permission rp ON p.pid = rp.pid
      INNER JOIN user_role ur ON ur.rid = rp.rid
      WHERE ur.uid = ? AND p.name = ?
    ";

    $q = $this->db->query($sql, array($uid, $perm));

    if($q->num_rows() > 0)
    {
      return TRUE;
    }

    page_access_denied();
  }

  function create_permission($perm)
  {
    // check if $perm exists
    $sql = "
      SELECT 1 FROM permissions p WHERE p.name = ?
    ";
    $q = $this->db->query($sql, array($perm));

    if($q->num_rows() == 0)
    {
      $sql = "
        INSERT INTO permissions (name) VALUES (?)
      ";
      $this->db->query($sql, array($perm));
    }
  }

  function permissions()
  {
    $items = array();
    $roles = array();
    $permissions = array();

    $sql = "
      SELECT * FROM roles ORDER BY rid
    ";
    $q_roles = $this->db->query($sql);

    $sql = "
      SELECT * FROM permissions ORDER BY pid
    ";
    $q_permissions = $this->db->query($sql);

    foreach($q_roles->result() as $role)
    {
      $roles[$role->rid] = $role->name;
      foreach($q_permissions->result() as $permission)
      {
        $items[$role->rid][$permission->pid] = 0;
      }
    }

    foreach($q_permissions->result() as $permission)
    {
      $permissions[$permission->pid] = $permission->name;
    }

    $sql = "
      SELECT * FROM role_permission ORDER BY rid, pid
    ";
    $q_roles_permissions = $this->db->query($sql);

    foreach($q_roles_permissions->result() as $role_permission)
    {
      $items[$role_permission->rid][$role_permission->pid] = 1;
    }

    return array('items' => $items, 'roles' => $roles, 'permissions' => $permissions);
  }

  function save_permissions()
  {
    // delete all permissions
    $this->db->query("DELETE FROM role_permission");

    $data = $this->permissions();
    $items = $data['items'];
    $role_permission = $_POST['role_permission'];

    foreach($items as $rid => $role)
    {
      foreach($role as $pid => $permission)
      {
        if( ! isset($role_permission[$rid]))
        {
          continue;
        }

        if(isset($role_permission[$rid][$pid]) && ! empty($role_permission[$rid][$pid]))
        {
          $this->db->query("INSERT INTO role_permission (rid, pid) VALUES ($rid, $pid)");
        }
      }
    }
  }

  function roles()
  {
    $q = $this->db->get('roles');
    return $q->result();
  }

  function has_permission($uid, $perm)
  {
    // root user always has full permissions
    if($uid == 1)
    {
      return TRUE;
    }

    $sql = "
      SELECT 1 FROM permissions p
      INNER JOIN role_permission rp ON p.pid = rp.pid
      INNER JOIN user_role ur ON ur.rid = rp.rid
      WHERE ur.uid = ? AND p.name = ?
    ";

    $q = $this->db->query($sql, array($uid, $perm));

    if($q->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }
}

/* End of file permission_model.php */
/* Location: ./application/models/permission_model.php */
