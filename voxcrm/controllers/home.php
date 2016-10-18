<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    if($this->session->userdata('mail'))
    {
      redirect('admin');
    }

    $errors = array('login' => '');

    $this->load->library('form_validation');
    $this->form_validation->set_rules('mail', 'Email address', 'required|valid_email');
    $this->form_validation->set_rules('pass', 'Password', 'required|min_length[4]');

    if($this->form_validation->run() !== false)
    {
      // validation passed
      $this->load->model('user_model');
      $res = $this
              ->user_model
              ->login(
                $this->input->post('mail'),
                $this->input->post('pass')
              );

      if($res !== false)
      {
        // person has account
        $this->session->set_userdata((array)$res);
        redirect('admin');
      }
      else
      {
        $errors['login'] = 'Log-in failed.'; 
      }
    }

    $data = array_merge(array('template' => 'home'), $errors);
    $this->load->view('template', $data);
  }

  public function logout()
  {
    $this->session->sess_destroy();
    redirect('home');
  }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
