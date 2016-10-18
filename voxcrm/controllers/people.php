<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class People extends MY_Controller
{
  function __construct()
  {
    parent::__construct();

    $this->load->library('form_validation');
    $this->load->library('pagination');
    $this->load->model('schedule_model');
  }

  function delete($type = 'student', $id)
  {
    $this->permission_model->check_permission($this->user->uid, 'manage people');

    if($type === 'staff' && $id == 1)
    {
      $this->session->set_flashdata('error', 'Cannot delete root admin user');
      redirect('people/search/' . $type);
    }

    // check if person is still in active event
    $result = null;
    if($type == 'staff')
    {
      $result = $this->db
        ->query('SELECT s.eid FROM schedules s
         WHERE s.staff_id = ? AND (s.status = ? OR s.status = ?)', array($id, Schedule_model::STATUS_PENDING, Schedule_model::STATUS_PAID_PENDING))->row();
    }
    else
    {
      $result = $this->db
        ->query('SELECT s.eid FROM schedules s
        INNER JOIN events e ON e.eid = s.eid
        WHERE e.student_id = ? AND (s.status = ? OR s.status = ?)', array($id, Schedule_model::STATUS_PENDING, Schedule_model::STATUS_PAID_PENDING))->row();
    }

    if( ! empty($result))
    {
      $this->session->set_flashdata('error', 'This person is in active event and cannot be deleted.');
      redirect('people/search/' . $type);
    }

    $this->session->set_flashdata('message', 'Person deleted.');
    $this->user_model->delete($type, $id);
    redirect('people/search/' . $type);
  }

  function search_json($type = 'student')
  {
    $this->load->library('output');

    $term = '';
    if(isset($_GET['term']) && !empty($_GET['term']))
    {
      $term = $_GET['term'];
    }

    $people = $this->user_model->search($type, $term);

    $items = array();
    foreach($people['items'] as $item)
    {
      $items[] = (object)array(
        'label' => "$item->first_name $item->last_name ($item->mail)",
        'value' => $item->sid
      );
    }

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($items));
  }

  function search($type = 'student', $q = '')
  {
    $this->permission_model->check_permission($this->user->uid, 'manage people');

    $title = 'Search people';

    $limit = 20;
    $offset = $this->uri->segment(5) ? $this->uri->segment(5) : 0;

    // prevent searching for null
    if($q === 'null')
    {
      $q = '';
    }
    $people = $this->user_model->search($type, $q, $offset, $limit);

    if($people['count'])
    {
      // use default 'null' if empty query for pagination to work
      $arg_4 = $this->uri->segment(4) ? $this->uri->segment(4) : 'null';

      // config pagination
      $config = array(
        'base_url' => base_url() . 'people/search/' . $this->uri->segment(3) . '/' . $arg_4 . '/',
        'total_rows' => $people['count'],
        'per_page' => $limit,
        'uri_segment' => 5,
      );

      $this->pagination->initialize($config);

      $this->data = array(
        'title' => $title,
        'template' => 'people',
        'items' => $people['items'],
        'pagination' => $this->pagination->create_links(),
      );
    }
    else
    {
      $this->data = array('title' => $title, 'template' => 'people', 'message' => 'No data found.');
    }

    $this->add_asset('js/people.js');

    $this->render_page();
  }

  function student($sid = null, $op = 'add')
  {
    $this->permission_model->check_permission($this->user->uid, 'manage people');

    $title = 'Add student';
    if($sid)
    {
      $title = 'Edit student profile';
    }

    switch($op)
    {
      case 'save':
        if($sid)
        {
			$this->form_validation->set_rules('mail', 'Email address', 'required|valid_email');
        }
		else
		{
			//$this->form_validation->set_rules('mail', 'Email address', 'required|valid_email|is_unique[users.mail]');
			$this->form_validation->set_rules('mail', 'Email address', 'required|valid_email');
		}

        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'callback_check_date');

        if($this->form_validation->run() !== false)
        {
          // validation pass
          $account = (object)array(
            'mail' => $this->input->post('mail'),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'dob' => date_to_system($this->input->post('dob')),
            'street' => $this->input->post('street'),
            'additional' => $this->input->post('additional'),
            'city' => $this->input->post('city'),
            'postcode' => $this->input->post('postcode'),
            'state' => $this->input->post('state'),
            'phone' => $this->input->post('phone'),
            'mobile' => $this->input->post('mobile'),
            'description' => $this->input->post('description'),
          );

          if($sid)
          {
            $account->sid = $sid;

            // unset uneccessary data
            //unset($account->mail);
          }
		  /*$check_email = $this->user_model->check_email($account->mail);
		  if (!empty($check_email->sid) && $check_email->sid != $sid)
		  {
			$this->session->set_flashdata('error', 'Email already exists.');
		  }
		  else
		  {*/
			  $sid = $this->user_model->save_student($account);

			  $this->session->set_flashdata('message', 'Student profile has been saved.');
		  //}
          redirect('people/student/' . $sid);
        }

        break;
    }

    $account = $this->user_model->prepare_empty_user();
    $account->sid = FALSE;
    if($sid)
    {
      $account = $this->user_model->student_info($sid);

      if( ! $account)
      {
        page_not_found();
      }

      $account->dob = date_to_display($account->dob);
    }

    $this->data = array('title' => $title, 'template' => 'student', 'account' => $account);
    $this->add_asset('js/people.js');
    $this->render_page();
  }
	private function _write_log( $msg )
	{
		$file = dirname(__FILE__).'/ezy_log.log';
		
		$date = date('m/d/Y h:i:s a');
		
		$msg = "[ $date ]: \n".$msg;
		$msg .= "\r\n\r\n";
		// The new person to add to the file
		// Write the contents to the file, 
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		file_put_contents($file, $msg, FILE_APPEND | LOCK_EX);
	}
  function staff($uid = null, $op = 'add')
  {
    // checking permission
    if(empty($uid))
    {
      $this->permission_model->check_permission($this->user->uid, 'manage people');
    }
    else
    {
      // only check permission if user attempt to edit different profile account
      if($uid !== $this->user->uid)
      {
        $this->permission_model->check_permission($this->user->uid, 'manage people');
      }
    }

    $can_manage_people = $this->permission_model->has_permission($this->user->uid, 'manage people');

    $title = 'Add teacher / admin';
    if($uid)
    {
      $title = 'Edit profile';
    }

    switch($op)
    {
      case 'save':
        if($uid)
        {
          $this->form_validation->set_rules('pass', 'Password', 'min_length[4]|matches[pass1]');
		  $this->form_validation->set_rules('mail', 'Email address', 'required|valid_email');
        }
        else
        {
          $this->form_validation->set_rules('mail', 'Email address', 'required|valid_email|is_unique[users.mail]');
          $this->form_validation->set_rules('pass', 'Password', 'required|min_length[4]|matches[pass1]');
        }

        if($can_manage_people)
        {
          $this->form_validation->set_rules('roles', 'Role', 'required|isset');
        }

        $this->form_validation->set_rules('dob', 'Date of Birth', 'callback_check_date');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('rate', 'Rate', 'required|greater_than[0]');
        $this->form_validation->set_rules('availability', 'Availability', 'required|greater_than[0]');

        if($this->form_validation->run() !== false)
        {
          // validation pass
          $account = (object)array(
            'mail' => $this->input->post('mail'),
            'pass' => $this->input->post('pass'),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'dob' => date_to_system($this->input->post('dob')),
            'street' => $this->input->post('street'),
            'additional' => $this->input->post('additional'),
            'city' => $this->input->post('city'),
            'postcode' => $this->input->post('postcode'),
            'state' => $this->input->post('state'),
            'phone' => $this->input->post('phone'),
            'mobile' => $this->input->post('mobile'),
            'description' => $this->input->post('description'),
            'rate' => $this->input->post('rate'),
            'availability' => $this->input->post('availability')
          );

          if($uid)
          {
            $account->uid = $uid;

            // unset uneccessary data when editing
            //unset($account->mail);
            if(empty($account->pass))
            {
              unset($account->pass);
            }
          }
		  $check_email = $this->user_model->check_email_2($account->mail);
		  if (!empty($check_email->uid) && $check_email->uid != $uid)
		  {
				$this->session->set_flashdata('error', 'Email already exists.');
		  }
		  else
		  {
			  $uid = $this->user_model->save_staff($account);

			  if($can_manage_people)
			  {
				// set roles
				$roles = $this->input->post('roles');
				$this->user_model->set_roles($uid, $roles);
			  }

			  $this->session->set_flashdata('message', 'User profile has been saved.');
		  }
          redirect('people/staff/' . $uid);
        }

        break;
    }

    $account = $this->user_model->prepare_empty_user();
    if($uid)
    {
      $account = $this->user_model->staff_info($uid);
      if( ! $account)
      {
        page_not_found();
      }

      $account->dob = date_to_display($account->dob);
      $account->roles = $this->user_model->roles($uid);
    }

    $roles = array();
    if($can_manage_people)
    {
      // roles data
      $roles = $this->permission_model->roles();
    }

    $this->data = array('title' => $title, 'template' => 'staff', 'account' => $account, 'roles' => $roles);
    $this->render_page();
  }

  function check_date($date)
  {
    if( empty($date) )
	{
		$date = $this->input->post('dob');
	}
	
	if ( !empty($date) )
	{
		$date_b = explode('/', $date);
		if (count($date_b) === 3)
		{
		  if ( is_numeric( $date_b[1] ) && is_numeric( $date_b[0] ) && is_numeric( $date_b[2] ) )
		  {
			  if ( ! checkdate( $date_b[1], $date_b[0], $date_b[2] ) )
			  {
				$this->form_validation->set_message('check_date', 'The %s field has to be in this format: dd/mm/yyyy.');
				return FALSE;
			  }
		  }
		  else
		  {
				$this->form_validation->set_message('check_date', 'The %s field has to be in this format: dd/mm/yyyy.');
				return FALSE;
		  }
		}
		else
		{
			$this->form_validation->set_message('check_date', 'The %s field has to be in this format: dd/mm/yyyy.');
			return FALSE;
		}
	}

    return TRUE;
  }
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */