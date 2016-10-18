<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 27/12/12
 * Time: 9:59 AM
 * To change this template use File | Settings | File Templates.
 */
class MY_Controller extends CI_Controller
{
  protected $data = array();
  private $assets = array();
  protected $user;

  function __construct()
  {
    parent::__construct();

    $this->user = new stdClass();

    $this->load->model('user_model');
    $this->_check_auth();

    $this->load->model('permission_model');
    $this->add_asset('$.extend(VSACRM, {base_url: "' . $this->config->item('base_url') . '"});', 'inline_js');

    date_default_timezone_set('Australia/Victoria');
  }


  private function _check_auth()
  {
    if( ! $this->session->userdata('mail'))
    {
      redirect('home');
    }
    else
    {
      $this->user = (object)$this->session->all_userdata();
      $this->user = (object)array_merge((array)$this->user_model->staff_info($this->user->uid), (array)$this->user);
      $this->user->roles = $this->user_model->roles($this->user->uid);
    }
  }

  function add_asset($file, $type = 'js')
  {
    switch($type)
    {
      case 'js':
        $this->assets['js'][] = $file;
        $this->assets['js'] = array_unique($this->assets['js']);
        break;

      case 'inline_js':
        $this->assets['inline_js'][] = $file;
        $this->assets['inline_js'] = array_unique($this->assets['inline_js']);
        break;

      case 'external_js':
        $this->assets['external_js'][] = $file;
        $this->assets['external_js'] = array_unique($this->assets['external_js']);
        break;

      case 'css':
        $this->assets['css'][] = $file;
        $this->assets['css'] = array_unique($this->assets['css']);
        break;

      case 'print_css':
        $this->assets['print_css'][] = $file;
        $this->assets['print_css'] = array_unique($this->assets['print_css']);
        break;
    }
  }

  function render_page()
  {
    $menu = array();

    $this->config->load('menu');
    if(in_array('admin', $this->user->roles))
    {
      $menu = $this->config->item('menu_admin');
    }
    else
    {
      $menu = $this->config->item('menu_teacher');
    }

    // load both admin/teacher menu
    if(in_array('admin', $this->user->roles) && in_array('teacher', $this->user->roles))
    {
      $menu = array_merge($this->config->item('menu_teacher'), $this->config->item('menu_admin'));
    }

    // check for successful message
    if($message = $this->session->flashdata('message'))
    {
      $this->data = array_merge($this->data, array('message' => $message));
    }

    // check for error message
    if($message = $this->session->flashdata('error'))
    {
      $this->data = array_merge($this->data, array('error' => $message));
    }

    $this->data = array_merge($this->data, $this->assets, array('menu' => $menu), array('user' => $this->user));
    $this->load->view('template', $this->data);
  }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
