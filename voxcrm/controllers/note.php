<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Note extends MY_Controller
{
  function __construct()
  {
    parent::__construct();

    $this->load->model('note_model');
    $this->load->model('user_model');
    $this->load->library('form_validation');
    $this->load->library('pagination');
    $this->load->library('typography');
  }

  function view($nid)
  {
    $this->permission_model->check_permission($this->user->uid, 'manage notes');

    $note = $this->note_model->notes($nid);

    if( ! $note)
    {
      page_not_found();
    }
    else
    {
      $note->student = $this->user_model->student_info($note->sid);
    }

    $this->data = array(
      'title' => $note->title,
      'template' => 'note_view',
      '_note' => $note,
    );

    $this->render_page();
  }

  function search($uid = 0)
  {
    if( empty($uid)) {
      show_404('Page does not exist');
    }

    $this->permission_model->check_permission($this->user->uid, 'manage notes');

    $title = 'Notes';

    $limit = 20;
    $offset = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

    $notes = $this->note_model->search($uid, $offset, $limit);

    if($notes['count'])
    {
      // config pagination
      $config = array(
        'base_url' => base_url() . 'note/search/' . $this->uri->segment(3),
        'total_rows' => $notes['count'],
        'per_page' => $limit,
        'uri_segment' => 4,
      );

      $this->pagination->initialize($config);

      foreach($notes['result'] as $i => $note)
      {
        $notes['result'][$i]->student = $this->user_model->student_info($note->sid);
      }

      $this->data = array(
        'title' => $title,
        'template' => 'notes',
        'notes' => $notes['result'],
        'pagination' => $this->pagination->create_links(),
      );
    }
    else
    {
      $this->data = array(
        'title' => $title,
        'template' => 'notes',
        'notes' => null,
        'future_notes' => null,
      );
    }

    $this->add_asset('js/notes.js');
    $this->render_page();
  }

  function manage($nid = null, $op = 'add')
  {
    $this->permission_model->check_permission($this->user->uid, 'manage notes');

    $title = 'Add Notes';

    switch($op)
    {
      case 'save':
        $this->form_validation->set_rules('student', 'Student', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('body', 'Body', 'required');

        if($this->form_validation->run() !== false)
        {
          // validation pass
          $note = (object)array(
            'nid' => $nid ? $nid : null,
            'uid' => $this->user->uid,
            'sid' => $this->input->post('student'),
            'title' => $this->input->post('title'),
            'body' => $this->input->post('body'),
            'created' => time(),
          );

          $nid = $this->note_model->save($note);
          $this->session->set_flashdata('message', 'Note has been saved.');

          $destination = $this->input->post('destination');
          $view = $this->input->post('view');
          $start = $this->input->post('start');

          if($destination)
          {
            redirect($destination . "?view=$view&start=$start");
          }

          redirect('note/search/' . $this->input->post('student'));
        }

        break;

      case 'delete':
        $this->note_model->delete($nid);
        $this->session->set_flashdata('message', 'Note has been deleted.');
        redirect('note/search/' . $this->input->get('sid'));
        break;
    }

    $note = $this->note_model->prepare_empty_note();
    if($nid)
    {
      $title = 'Edit note';
      $note = $this->note_model->notes($nid);

      if( ! $note)
      {
        page_not_found();
      }
      else
      {
        $note->student = $this->user_model->student_info($note->sid);
      }
    }

    if( ! empty($_GET['sid']))
    {
      $default_student = $this->user_model->student_info($_GET['sid']);
      $this->data['default_student'] = $default_student;
    }

    if( ! empty($_GET['destination']))
    {
      $this->data['destination'] = $_GET['destination'];
    }

    $calendar_view = array(
      'view' => 'agendaWeek',
      'start' => time()
    );

    if( ! empty($_GET['current_calendar_view']) &&
      ! empty($_GET['current_calendar_start'])) {
      $calendar_view = array(
        'view' => $_GET['current_calendar_view'],
        'start' => $_GET['current_calendar_start']
      );
    }
    $this->data['calendar_view'] = $calendar_view;

    $this->data += array('title' => $title, 'template' => 'note', '_note' => $note);

    // assets
    $this->add_asset('js/vendor/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js');
    $this->add_asset('js/vendor/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css', 'css');
    $this->add_asset('js/note.js?20130614');

    $this->render_page();
  }
}

/* End of file note.php */
/* Location: ./application/controllers/note.php */
