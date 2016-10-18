<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller
{
  function __construct()
  {
    parent::__construct();

    $this->load->model('studio_model');
    $this->load->model('product_model');
    $this->load->library('form_validation');
    $this->load->library('typography');
    $this->load->model('schedule_model');
    $this->load->model('email_admin_reason');
  }

  function index()
  {
    redirect('calendar');
  }

  function studios()
  {
    $this->permission_model->check_permission($this->user->uid, 'manage studios');
    $studios = $this->studio_model->studios();
    $this->data = array('title' => 'Manage studios', 'template' => 'studios', 'studios' => $studios);
    $this->add_asset('js/studios.js');
    $this->render_page();
  }

  function studio($sid = null, $op = 'add')
  {
    $this->permission_model->check_permission($this->user->uid, 'manage studios');

    $title = 'Add studio';

    switch($op)
    {
      case 'save':
        $this->form_validation->set_rules('name', 'Name', 'required');

        if($this->form_validation->run() !== false)
        {
          // validation pass
          $studio = (object)array(
            'name' => $this->input->post('name'),
            'street' => $this->input->post('street'),
            'additional' => $this->input->post('additional'),
            'city' => $this->input->post('city'),
            'postcode' => $this->input->post('postcode'),
            'state' => $this->input->post('state'),
            'phone' => $this->input->post('phone'),
            'fax' => $this->input->post('fax'),
            'description' => $this->input->post('description'),
          );

          if($sid)
          {
            $studio->sid = $sid;
          }

          $sid = $this->studio_model->save($studio);
          $this->session->set_flashdata('message', 'Studio profile has been saved.');
          redirect('admin/studios');
        }

        break;

      case 'delete':
        // check if studio is still in active event
        $result = $this->db
          ->query('SELECT eid FROM schedules WHERE studio_id = ? AND (status = ? OR status = ?)', array($sid, Schedule_model::STATUS_PENDING, Schedule_model::STATUS_PAID_PENDING))->row();
        /*if( ! empty($result))
        {
          $this->session->set_flashdata('error', 'Studio is in active event and cannot be deleted.');
          redirect('admin/studios');
        }*/

        $this->studio_model->delete($sid);
        $this->session->set_flashdata('message', 'Studio has been deleted.');
        redirect('admin/studios');
        break;
    }

    $studio = $this->studio_model->prepare_empty_studio();
    if($sid)
    {
      $title = 'Edit studio';
      $studio = $this->studio_model->studios($sid);

      if( ! $studio)
      {
        page_not_found();
      }
    }

    $this->data = array('title' => $title, 'template' => 'studio', 'studio' => $studio);
    $this->render_page();
  }

  function products()
  {
    $this->permission_model->check_permission($this->user->uid, 'manage products');
    $products = $this->product_model->products();
    $this->data = array('title' => 'Manage products', 'template' => 'products', 'products' => $products);
    $this->add_asset('js/products.js');
    $this->render_page();
  }

  function product($pid = null, $op = 'add')
  {
    $this->permission_model->check_permission($this->user->uid, 'manage products');

    $title = 'Add product';

    switch($op)
    {
      case 'save':
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('lessons', 'Lessons', 'required|greater_than[0]|is_natural');
        $this->form_validation->set_rules('price', 'Price', 'required|is_natural');

        if($this->form_validation->run() !== false)
        {
          // validation pass
          $product = (object)array(
            'name' => $this->input->post('name'),
            'lessons' => $this->input->post('lessons'),
            'price' => $this->input->post('price'),
            'description' => $this->input->post('description'),
            'recurring' => $this->input->post('recurring'),
          );

          if($pid)
          {
            $product->pid = $pid;
          }

          $pid = $this->product_model->save($product);
          $this->session->set_flashdata('message', 'Product has been saved.');
          redirect('admin/products');
        }

        break;

      case 'delete':
        // check if studio is still in active event
        $result = $this->db
          ->query('SELECT eid FROM events WHERE product_id = ? AND status = 1', array($pid))->row();
        if( ! empty($result))
        {
          $this->session->set_flashdata('error', 'Product is in active event and cannot be deleted.');
          redirect('admin/products');
        }

        $this->product_model->delete($pid);
        $this->session->set_flashdata('message', 'Product has been deleted.');
        redirect('admin/products');
        break;
    }

    $product = $this->product_model->prepare_empty_product();
    if($pid)
    {
      $title = 'Edit product';
      $product = $this->product_model->products($pid);
      $product->in_active_event = false;

      // check if studio is still in active event
      $result = $this->db
        ->query('SELECT eid FROM events WHERE product_id = ? AND status = 1', array($pid))->row();
      if( ! empty($result))
      {
        $product->in_active_event = true;
      }


      if( ! $product)
      {
        page_not_found();
      }
    }

    $this->data = array('title' => $title, 'template' => 'product', 'product' => $product);
    $this->render_page();
  }

  function permissions()
  {
    $this->permission_model->check_permission($this->user->uid, 'manage permissions');
    $data = $this->permission_model->permissions();
    $this->data = array('title' => 'Manage permissions', 'template' => 'permissions', 'data' => $data);
    $this->render_page();
  }

  function save_permissions()
  {
    $this->permission_model->save_permissions();
    $this->session->set_flashdata('message', 'Permissions saved.');
    redirect('admin/permissions');
  }

  function technical_email()
  {
    $start = $this->input->post('email_start');
    $view = $this->input->post('email_view');

    $this->form_validation->set_rules('email_message', 'Message', 'required');
    if($this->form_validation->run() !== FALSE)
    {
      $teacher = $this->input->post('email_teacher');
      $sid = $this->input->post('email_studio');
      $reason = $this->input->post('email_reason');
      $message = $this->input->post('email_message');
      $studio = $this->studio_model->studios($sid);

      // store to db
      $email_admin_data = (object)array(
        'uid' => $this->input->post('email_uid'),
        'sid' => $sid,
        'reason' => $reason,
        'message' => $message,
        'created' => time(),
      );
      $this->email_admin_reason->save($email_admin_data);

      my_send_mail(
        array(
          'to' => 'contact@voxsingingacademy.com',
          'subject' => "TEACHER EMAIL | Vox Singing Academy",
          'message' => "
<p>Hi admin,</p>
<p>You've got following message from teacher {$teacher}:</p>
<p><strong>Studio: </strong>{$studio->name}</p>
<p><strong>Reason: </strong>" . $this->email_admin_reason->reason_value($reason) . "</p>
<p><strong>Message: </strong><br />
" . $this->typography->auto_typography($message) . "</p>
<p>Thank you!</p>
<p><strong>Vox Singing Academy | Australia's Voice Training Specialist</strong></p>
          "
        )
      );

      $this->session->set_flashdata('message', 'Email has been sent.');
    }
    else
    {
      $this->session->set_flashdata('error', validation_errors());
    }

    redirect("calendar?view=$view&start=$start");
  }
}


/* End of file admin.php */
/* Location: ./application/controllers/admin.php */