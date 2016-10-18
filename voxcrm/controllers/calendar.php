<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	class Calendar extends MY_Controller
	{
		const BACK_DEFAULT = '#336600';
		const TEXT_DEFAULT = '#ffffff';
		const BACK_ONE_LEFT = '#cc3333';
		const TEXT_ONE_LEFT = '#ffffff';
		const BACK_TWO_LEFT = '#cccc00';
		const TEXT_TWO_LEFT = '#000000';
		const BACK_FIRST_LESSON = '#6699ff';
		const TEXT_FIRST_LESSON = '#000000';
		const BACK_CANCELED_OVER_24 = '#ababab';
		const TEXT_CANCELED_OVER_24 = '#000';
		const BACK_CANCELED_UNDER_24 = '#575757';
		const TEXT_CANCELED_UNDER_24 = '#ffffff';
		const BACK_FIRST_LESSON_UNPAID = '#efefef';
		const TEXT_FIRST_LESSON_UNPAID = '#aa0000';
		
		const BACK_GREEN_LESSON_PAID = '#336600';
		const TEXT_GREEN_LESSON_PAID = '#ffffff';
		
		function __construct()
		{
			parent::__construct();
			
			$this->load->model('event_model');
			$this->load->model('product_model');
			$this->load->model('user_model');
			$this->load->model('studio_model');
			$this->load->model('schedule_model');
			$this->load->model('note_model');
			$this->load->model('reason_model');
			$this->load->model('delete_reason_model');
			$this->load->library('form_validation');
			$this->load->library('typography');
			$this->load->model('email_admin_reason');
		}
		
		function event($eid_cid = null, $op = 'add')
		{
			$this->permission_model->check_permission($this->user->uid, 'manage event');
			
			$title = 'Add event';
			if($eid_cid)
			{
				$title = 'Edit lessons';
			}
			
			switch($op)
			{
				case 'save':
				if(empty($eid_cid))
				{
					$this->form_validation->set_rules('name', 'Name', 'required');
					$this->form_validation->set_rules('student', 'Student', 'required');
					$this->form_validation->set_rules('teacher', 'Teacher', 'required');
					$this->form_validation->set_rules('product', 'Product', 'required');
					$this->form_validation->set_rules('studio', 'Studio', 'required');
					$this->form_validation->set_rules('scheduled_date', 'Date', 'required|callback_date_time_check');
					$this->form_validation->set_rules('scheduled_time_from', 'From', 'required');
					$from = $this->input->post('scheduled_time_from');
					$this->form_validation->set_rules('scheduled_time_to', 'To', 'required|greater_than[' . $from . ']');
					
					if($this->form_validation->run() !== false)
					{
						// validation pass
						$event = (object)array(
						'name' => $this->input->post('name'),
						'student_id' => $this->input->post('student'),
						'product_id' => $this->input->post('product'),
						'description' => $this->input->post('description'),
						'status' => Event_model::STATUS_UNPAID,
						'created' => time(),
						);
						
						// fetching necessary objects
						$event->student = $this->user_model->student_info($event->student_id);
						$event->product = $this->product_model->products($event->product_id);
						
						// calculating scheduled date/time
						$scheduled_date = $this->input->post('scheduled_date');
						$scheduled_time_from = time_value($this->input->post('scheduled_time_from'));
						$scheduled_time_to = time_value($this->input->post('scheduled_time_to'));
						$scheduled_date_arr = explode('/', $scheduled_date);
						
						$start_scheduled = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_from");
						$start_ended = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_to");
						
						// generating schedules
						$product = $event->product;
						for($i = 0; $i < $product->lessons; $i++)
						{
							$weeks = $i * $event->product->recurring;
							$event->schedules[] = (object)array(
							'sequence' => $i+1,
							'start' => strtotime("+{$weeks} weeks", $start_scheduled),
							'end' => strtotime("+{$weeks} weeks", $start_ended),
							'status' => schedule_model::STATUS_PENDING,
							'staff_id' => $this->input->post('teacher'),
							'studio_id' => $this->input->post('studio'),
							);
						}
						
						// checking if each of schedule is valid
						$valid_schedules = TRUE;
						$errors = array();
						$sched_no = '';

						$dt = '';
						
						$new_n = '';
						$last_n = '';
						$sd = '';
					
						foreach($event->schedules as $schedule)
						{
							$schedule->event = $event;
							//ADD_EVENT_IS_VALID_2
							$rs = $this->schedule_model->is_valid_2($schedule);
							
							if( !empty($rs) )
							{
								
								$valid_schedules = false;
								$sched_no .= '#'.$schedule->sequence.', ';
								
								foreach($rs as $rd => $rk)
								{
									$new_n = $rk->name;
									
									if ( $new_n != $last_n )
									{
										$sd = '<strong>Student: '.$rk->name.'</strong>';
									}
									
									$dt .= '<div class="indent-32">'.'<li>Lesson #'.$rk->sequence.' - '.date("M j, Y", $rk->start);
									$dt .= '<div class="indent-32">Start: '.date("g:i a", $rk->start).'<br>';
									$dt .= 'End: '.date("g:i a", $rk->end).'</div></div></li>';
								}
								
								$dt = $sd.$dt;
								
								$sd = '';
								$last_n = $new_n;
								
							}
						}
											
						if ( ! $valid_schedules)
						{

							$errs = '<p>Unable to proceed. The schedule for lesson(s) ' . rtrim($sched_no, ', ') . ' conflicts with schedule of other lesson by the same teacher. Please double check the calendar.</p>';
							$errs .= '<p>Conflicts are as follows: </p>';
							$errs .=  '<div class="panel error-box">'.$dt.'</div>';
							$errors[] = $errs;
							
							$this->data['error'] = implode('', $errors);
							
							$this->session->set_flashdata('error',implode('', $errors));
						
						}
						else
						{
							$this->event_model->save($event);
							
							// send mail
							$mail = $this->input->post('email_notification');
							if($mail)
							{
								$this->_send_mail_student($event);
							}
							
							$this->session->set_flashdata('message', 'Event and its schedules has been created.');
							
							redirect('calendar?view=agendaWeek&start=' . $start_scheduled);
						}
					}
				}
				else
				{
					$this->form_validation->set_rules('name', 'Name', 'required');
					$this->form_validation->set_rules('teacher', 'Teacher', 'required');
					$this->form_validation->set_rules('studio', 'Studio', 'required');
					
					if($this->form_validation->run() !== false)
					{
						list($eid, $cid) = explode('_', $eid_cid);
						$event = $this->event_model->events($eid);
						$event->name = $this->input->post('name');
						$event->description = $this->input->post('description');
						
						// save event
						$this->event_model->save($event);
						
						// edited schedule
						$edited_schedules = array();
						$edited_schedule = $this->schedule_model->schedules($cid);
						
						if($this->input->post('all_lessons') == 'all')
						{
							// get all schedules of the event
							$event->schedules = $this->schedule_model->event_schedules($eid);
							foreach($event->schedules as $schedule)
							{
								if($schedule->sequence >= $edited_schedule->sequence &&
								$schedule->status == Schedule_model::STATUS_PENDING)
								{
									$schedule->staff_id = $this->input->post('teacher');
									$schedule->studio_id = $this->input->post('studio');
									$this->schedule_model->save($schedule);
									$edited_schedules[] = $schedule;
								}
							}
						}
						else
						{
							$edited_schedule->staff_id = $this->input->post('teacher');
							$edited_schedule->studio_id = $this->input->post('studio');
							$this->schedule_model->save($edited_schedule);
							$edited_schedules[] = $edited_schedule;
						}
						
						// send mail
						$mail = $this->input->post('email_notification');
						if($mail)
						{
							$this->_edit_lessons_send_mail_student($event, $edited_schedules);
						}
						$this->session->set_flashdata('message', 'Lesson has been updated.');
						redirect('calendar');
					}
				}
				
				break;
			}
			
			if( ! empty($_GET['sid']))
			{
				$default_student = $this->user_model->student_info($_GET['sid']);
				$this->data['default_student'] = $default_student;
			}
			
			$product_options = array('' => 'Please select') + $this->product_model->list_options();
			$teacher_options = array('' => 'Please select') + $this->user_model->list_options();
			$studio_options = array('' => 'Please select') + $this->studio_model->list_options();
			
			$event = $this->event_model->prepare_empty_event();
			if($eid_cid)
			{
				list($eid, $cid) = explode('_', $eid_cid);
				
				$event = $this->event_model->events($eid);
				if( ! $event)
				{
					page_not_found();
				}
				
				$event->student = $this->user_model->student_info($event->student_id);
				$event->product = $this->product_model->products($event->product_id);
				$event->schedule = $this->schedule_model->schedules($cid);
			}
			
			// assets
			$this->add_asset('js/vendor/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js');
			$this->add_asset('js/vendor/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css', 'css');
			$this->add_asset('js/events.js');
						
			// data
			$this->data = array_merge($this->data,
			array(
			'title' => $title,
			'template' => 'event',
			'event' => $event,
			'product_options' => $product_options,
			'teacher_options' => $teacher_options,
			'studio_options' => $studio_options,
			)
			);
			
			$this->render_page();
		}
		
		function date_time_check()
		{
			$scheduled_date = $this->input->post('scheduled_date');
			$scheduled_time_from = time_value($this->input->post('scheduled_time_from'));
			$scheduled_time_to = time_value($this->input->post('scheduled_time_to'));
			$scheduled_date_arr = explode('/', $scheduled_date);
			
			$start_scheduled = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_from");
			$start_ended = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_to");
			
			if($start_scheduled >= $start_ended)
			{
				$this->form_validation->set_message('date_time_check', '"From" time must be greater than "to" time.');
				return FALSE;
			}
			
			return TRUE;
		}
		function rebook_date_time_check()
		{
			$scheduled_date = $this->input->post('rebook_date');
			$scheduled_time_from = time_value($this->input->post('rebook_time_from'));
			$scheduled_time_to = time_value($this->input->post('rebook_time_to'));
			$scheduled_date_arr = explode('/', $scheduled_date);
			
			$start_scheduled = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_from");
			$start_ended = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_to");
			
			if($start_scheduled >= $start_ended)
			{
				$this->form_validation->set_message('rebook_date', '"From" time must be greater than "to" time.');
				return FALSE;
			}
			
			return TRUE;
		}
		
		function reset_filter()
		{
			$this->session->unset_userdata(array(
			'studio_id_filter' => '',
			'teacher_id_filter' => ''
			));
			
			redirect('calendar');
		}
		
		function index()
		{
			$this->permission_model->check_permission($this->user->uid, 'view calendar');
			
			// assets
			$this->add_asset('js/vendor/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js');
			$this->add_asset('js/vendor/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css', 'css');
			$this->add_asset('js/vendor/fullcalendar-1.5.4/fullcalendar/fullcalendar.css', 'css');
			$this->add_asset('js/vendor/fullcalendar-1.5.4/fullcalendar/fullcalendar.print.css', 'print_css');
			$this->add_asset('js/vendor/fullcalendar-1.5.4/fullcalendar/fullcalendar.min.js');
			$this->add_asset('js/vendor/jquery.qtip-1.0.0-rc3.min.js');
			$this->add_asset('js/vendor/jquery.blockUI.js');
			$this->add_asset('js/events.js');
			$this->add_asset('js/calendar.js?20130509');
			
			$this->add_asset('$.extend(VSACRM, {
			studio_start_time: ' . $this->config->item('studio_start_time') . ',
			studio_end_time: ' . $this->config->item('studio_end_time') . '});', 'inline_js');
			
			if( ! empty($_GET['view']) &&
			! empty($_GET['start'])) {
				$this->add_asset('$.extend(VSACRM, {
				current_calendar_view: "' . $_GET['view'] . '",
				current_calendar_start: ' . $_GET['start'] . '});', 'inline_js');
			}
			
			if($_POST)
			{
				// add to session
				$this->session->set_userdata(array(
				'studio_id_filter' => $this->input->post('studio_id') ? $this->input->post('studio_id') : '',
				'teacher_id_filter' => $this->input->post('teacher_id') ? $this->input->post('teacher_id') : ''
				));
			}
			
			$studio_id_filter = $this->session->userdata('studio_id_filter');
			$teacher_id_filter = $this->session->userdata('teacher_id_filter');
			
			$this->add_asset('$.extend(VSACRM, {
			studio_id: "' . $studio_id_filter . '",
			teacher_id: "' . $teacher_id_filter . '"
			});', 'inline_js');
			
			// detect teacher view
			$view = 'admin';
			if(count($this->user->roles) == 1 && array_shift(array_values($this->user->roles)) == 'teacher')
			{
				$this->add_asset('$.extend(VSACRM, {
				teacher_id: ' . $this->user->uid .
				'});', 'inline_js');
				$view = 'teacher';
			}
			// pass $view into js
			$this->add_asset("$.extend(VSACRM, {view: '$view'});", 'inline_js');
			
			$schedule_statuses = $this->schedule_model->status_options();
			$this->add_asset("$.extend(VSACRM, {schedule_statuses: " . json_encode($schedule_statuses) . "});", 'inline_js');
			
			$title = 'Calendar';
			
			$admin_check = $this->user_model->has_role($this->user->uid, '1');
			
			if ($admin_check == true) {
				$product_options = $this->product_model->list_options();
				} else {
				$product_options = $this->product_model->single_lesson();
			}
			$this->data = array(
			'title' => $title,
			'template' => 'calendar',
			'view' => $view,
			'product_options' => $product_options,
			'user' => $this->user
			);
			
			$this->render_page();
		}
		
		function json()
		{
			$start = $_GET['start'];
			$end = $_GET['end'];
			
			$this->load->library('output');
			$studio_id = $teacher_id = null;
			
			if ( ! empty($_GET['studio_id']))
			{
				$studio_id = $_GET['studio_id'];
			}
			if ( ! empty($_GET['teacher_id']))
			{
				$teacher_id = $_GET['teacher_id'];
			}
			
			// fetch schedules & pass to js
			$schedules = array();
			if (empty($studio_id) && empty($teacher_id))
			{
				$schedules = $this->schedule_model->query(array(
				'start' => $start,
				'end' => $end,
				'filter' => '*'
				));
			}
			else if ( ! empty($studio_id) && empty($teacher_id))
			{
				$schedules = $this->schedule_model->query(array(
				'start' => $start,
				'end' => $end,
				'filter' => 'studio',
				'ids' => array(
				'studio_id' => $studio_id,
				)
				));
			}
			else if ( ! empty($teacher_id) && empty($studio_id))
			{
				$schedules = $this->schedule_model->query(array(
				'start' => $start,
				'end' => $end,
				'filter' => 'teacher',
				'ids' => array(
				'teacher_id' => $teacher_id,
				)
				));
			}
			else
			{
				$schedules = $this->schedule_model->query(array(
				'start' => $start,
				'end' => $end,
				'filter' => 'studio_teacher',
				'ids' => array(
				'studio_id' => $studio_id,
				'teacher_id' => $teacher_id,
				)
				));
			}
			
			// fetching title for each schedule
			static $events = array();
			foreach($schedules as $key => $schedule)
			{
				if (empty($events[$schedule->eid]))
				{
					$event = $this->event_model->events($schedule->eid);
					$event->description = $event->description;
					$event->student = $this->user_model->student_info($event->student_id);
					$event->product = $this->product_model->products($event->product_id);
					$events[$schedule->eid] = $event;
				}
				
				$schedules[$key]->teacher = $this->user_model->staff_info($schedules[$key]->staff_id);
				$schedules[$key]->studio = $this->studio_model->studios($schedules[$key]->studio_id);
				$schedules[$key]->event = $events[$schedule->eid];
			}
			
			$events = $this->_prepare_events($schedules);
			
			$this->output
			->set_content_type('application/json')
			->set_output(json_encode($events));
		}
		
		function schedule($op, $cid)
		{
			$calendar_view = array(
			'view' => 'agendaWeek',
			'start' => time()
			);
			
			if( ! empty($_POST['current_calendar_view']) &&
			! empty($_POST['current_calendar_start'])) {
				$calendar_view = array(
				'view' => $_POST['current_calendar_view'],
				'start' => $_POST['current_calendar_start']
				);
			}
			
			switch($op)
			{
				case 'status':
				// handling re-schedule for cancel over 24 hours
				if($this->input->post('status') == Schedule_model::STATUS_CANCELED_OVER_24)
				{
					$this->form_validation->set_rules('scheduled_date', 'Date', 'required|callback_date_time_check');
					$this->form_validation->set_rules('scheduled_time_from', 'From', 'required');
					$from = $this->input->post('scheduled_time_from');
					$this->form_validation->set_rules('scheduled_time_to', 'To', 'required|greater_than[' . $from . ']');
					
					if($this->form_validation->run() !== false)
					{
						// calculating scheduled date/time
						$scheduled_date = $this->input->post('scheduled_date');
						$scheduled_time_from = time_value($this->input->post('scheduled_time_from'));
						$scheduled_time_to = time_value($this->input->post('scheduled_time_to'));
						$scheduled_date_arr = explode('/', $scheduled_date);
						
						$start_scheduled = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_from");
						$start_ended = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_to");
						
						// old sequence (cancelled sequence)
						$old_schedule = $this->schedule_model->schedules($cid);
						
						// getting eid
						$result = $this->db->query('SELECT eid, status FROM schedules WHERE cid = ?', array($cid))->row();
						$eid = $result->eid;
						$sched_status = $result->status;
						// checks to see if the entire event has been paid for, so we can flag the new schedule as paid for if appropriate
						$event = $this->db->query('SELECT status FROM events WHERE eid = ?', array($eid))->row();
						$status = $event->status;
						
						if($sched_status == Schedule_model::STATUS_PAID_PENDING ) {
							$EventPaid = Schedule_model::STATUS_PAID_PENDING;
						}
						else {
							$EventPaid = Schedule_model::STATUS_PENDING;
						}
						
						//$this->_write_log( print_r($status, true) );
						// new schedule
						$new_schedule = (object)array(
							'start' => $start_scheduled,
							'end' => $start_ended,
							'status' => $EventPaid,
							'eid' => $eid,
							'staff_id' => $old_schedule->staff_id,
							'studio_id' => $old_schedule->studio_id,
						);
						
						
						
						// checking if new schedule is valid
						$valid_schedules = TRUE;
						$errors = array();
						$event = $this->event_model->events($eid);
						$new_schedule->event = $event;
						
						/*$this->_write_log( print_r( $asdasdasd, true) );*/
						//CANCEL_OVER_24_HOURS_IS_VALID_2
						$rs = $this->schedule_model->is_valid_2($new_schedule);
						
						
						if ( !empty($rs) )
						{
							$valid_schedules = FALSE;
						
							$dt = '';
							
							$new_n = '';
							$last_n = '';
							$sd = '';
							
							foreach($rs as $rd => $rk)
							{
								$new_n = $rk->name;
								
								if ( $new_n != $last_n )
								{
									$sd = '<strong>Student: '.$rk->name.'</strong>';
								}
								
								$dt .= '<div class="indent-32">'.'<li>Lesson #'.$rk->sequence.' - '.date("M j, Y", $rk->start);
								$dt .= '<div class="indent-32">Start: '.date("g:i a", $rk->start).'<br>';
								$dt .= 'End: '.date("g:i a", $rk->end).'</div></div></li>';
							}
							
							$dt = $sd.$dt;
							
							$sd = '';
							$last_n = $new_n;
							
							$errs = '<p>Unable to proceed. The schedule for lesson #'.$old_schedule->sequence . ' conflicts with schedule of other lesson by the same teacher. Please double check the calendar.</p>';
							$errs .= '<p>Conflicts are as follows: </p>';
							$errs .=  '<div class="panel error-box">'.$dt.'</div>';
							
							$errors[] = $errs;
													
						}
						
						if ( ! $valid_schedules)
						{	
							$this->session->set_flashdata('error',implode('', $errors));
						}
						else
						{
							// update old schedule
							$old_schedule->status = $this->input->post('status');
							$old_schedule->description = $this->input->post('description');
							$old_schedule->sequence = 0;
							$this->schedule_model->save($old_schedule);
							
							// append new schedule, rework schedule sequence
							$this->schedule_model->rework_schedules_sequence($new_schedule, $eid);
							
							$this->session->set_flashdata('message', 'Lesson status has been updated. A new lesson has been set. Start: ' .
							date('d/m/Y g:ia', $new_schedule->start) . ' - End: ' . date('d/m/Y g:ia', $new_schedule->end));
							
							// send mail
							$mail = $this->input->post('email_notification_1');
							if($mail)
							{
								$event->teacher = $this->user_model->staff_info($old_schedule->staff_id);
								$event->student = $this->user_model->student_info($event->student_id);
								$this->_cancel_mail_student($event, $old_schedule, $new_schedule);
							}
						}
					}
					else
					{
						$this->session->set_flashdata('error', validation_errors());
					}
				}
				// handling re-schedule for cancel within 24 hours
				else if($this->input->post('status') == Schedule_model::STATUS_CANCELED_IN_24)
				{
					$schedule = $this->schedule_model->schedules($cid);
					$schedule->status = $this->input->post('status');
					$schedule->description = $this->input->post('description_1');
					$this->schedule_model->save($schedule);
					
					$this->session->set_flashdata('message', 'Lesson status has been updated.');
					
					// send mail
					$mail = $this->input->post('email_notification_2');
					if($mail)
					{
						$event = $this->event_model->events($schedule->eid);
						$event->student = $this->user_model->student_info($event->student_id);
						$this->_cancel_under24_mail_student($event, $schedule);
					}
				}
				// handling for deleting event
				else if($this->input->post('status') == Schedule_model::STATUS_DELETED)
				{
					$this->form_validation->set_rules('description_2', 'Reason', 'required');
					
					if($this->form_validation->run() !== false)
					{
						$schedule = $this->schedule_model->schedules($cid);
						$eid = $schedule->eid;
						$this->event_model->delete($eid, $this->input->post('description_2'));
						
						// send mail
						$mail = $this->input->post('email_notification_3');
						if($mail)
						{
							$event = $this->event_model->events($eid);
							$event->teacher = $this->user_model->staff_info($schedule->staff_id);
							$event->student = $this->user_model->student_info($event->student_id);
							$event->product = $this->product_model->products($event->product_id);
							$this->_delete_mail_student($event);
						}
						
						$this->session->set_flashdata('message', 'Event has been deleted.');
					}
					else
					{
						$this->session->set_flashdata('error', validation_errors());
					}
				}
				else if($this->input->post('status') == 'rebook')
				{
					$this->form_validation->set_rules('product', 'Product', 'required');
					$this->form_validation->set_rules('rebook_date', 'Date', 'required|callback_rebook_date_time_check');
					$this->form_validation->set_rules('rebook_time_from', 'From', 'required');
					$from = $this->input->post('rebook_time_from');
					$this->form_validation->set_rules('rebook_time_to', 'To', 'required|greater_than[' . $from . ']');
					
					// validation pass
					if( $this->form_validation->run() !== false )
					{
						// getting eid
						$result = $this->db->query('SELECT eid FROM schedules WHERE cid = ?', array($cid))->row();
						$eid = $result->eid;
						
						$original_event = $this->event_model->events($eid);
						
						$admin_check = $this->user_model->has_role($this->user->uid, '1');
						
						if ($admin_check === true)
						{
							$e_status = Event_model::STATUS_UNPAID;
						}
						else
						{
							$e_status = Event_model::STATUS_UNPAID;
						}
						//$this->_write_log( print_r($e_status, true) );
						$event = (object)array(
						'name' => $original_event->name,
						//'staff_id' => $original_event->staff_id,
						'student_id' => $original_event->student_id,
						//'studio_id' => $original_event->studio_id,
						'product_id' => $this->input->post('product'),
						'description' => $original_event->description,
						'status' => $e_status /*Event_model::STATUS_ACTIVE /*Event_model::STATUS_UNPAID*/,
						'created' => time(),
						);
						
						// fetching necessary objects
						$event->student = $this->user_model->student_info($event->student_id);
						$event->product = $this->product_model->products($event->product_id);
						
						// calculating scheduled date/time
						$scheduled_date = $this->input->post('rebook_date');
						$scheduled_time_from = time_value($this->input->post('rebook_time_from'));
						$scheduled_time_to = time_value($this->input->post('rebook_time_to'));
						$scheduled_date_arr = explode('/', $scheduled_date);
						
						$start_scheduled = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_from");
						$start_ended = strtotime("$scheduled_date_arr[2]-$scheduled_date_arr[1]-$scheduled_date_arr[0] $scheduled_time_to");
						
						// generating schedules
						$product = $event->product;
						$old_schedule = $this->schedule_model->schedules($cid);
						
						$admin_check = $this->user_model->has_role($this->user->uid, '1');
						if ($admin_check === true)
						{
							$e_status = schedule_model::STATUS_PENDING;
						}
						else
						{
							$e_status = schedule_model::STATUS_PENDING;
						}
						//$this->_write_log( print_r($e_status, true) );
						for($i = 0; $i < $product->lessons; $i++)
						{
							$weeks = $i * $event->product->recurring;
							$event->schedules[] = (object)array(
							'sequence' => $i+1,
							'start' => strtotime("+{$weeks} weeks", $start_scheduled),
							'end' => strtotime("+{$weeks} weeks", $start_ended),
							'status' => $e_status /*schedule_model::STATUS_PAID_PENDING /*schedule_model::STATUS_PENDING*/,
							'staff_id' => $old_schedule->staff_id,
							'studio_id' => $old_schedule->studio_id,
							);
						}
						
						// checking if each of schedule is valid
						$valid_schedules = TRUE;
						$errors = array();
						$sched_no = '';

						$dt = '';
						
						$new_n = '';
						$last_n = '';
						$sd = '';
					
						foreach($event->schedules as $schedule)
						{
							$schedule->event = $event;
							//REBOOK_IS_VALID_2
							$rs = $this->schedule_model->is_valid_2($schedule);
							
							if( !empty($rs) )
							{
								
								$valid_schedules = false;
								$sched_no .= '#'.$schedule->sequence.', ';
								
								foreach($rs as $rd => $rk)
								{
									$new_n = $rk->name;
									
									if ( $new_n != $last_n )
									{
										$sd = '<strong>Student: '.$rk->name.'</strong>';
									}
									
									$dt .= '<div class="indent-32">'.'<li>Lesson #'.$rk->sequence.' - '.date("M j, Y", $rk->start);
									$dt .= '<div class="indent-32">Start: '.date("g:i a", $rk->start).'<br>';
									$dt .= 'End: '.date("g:i a", $rk->end).'</div></div></li>';
								}
								
								$dt = $sd.$dt;
								
								$sd = '';
								$last_n = $new_n;
								
							}
						}
						$this->_write_log($valid_schedules);
						if ( ! $valid_schedules)
						{

							$errs = '<p>Unable to proceed. The schedule for lesson(s) ' . rtrim($sched_no, ', ') . ' conflicts with schedule of other lesson by the same teacher. Please double check the calendar.</p>';
							$errs .= '<p>Conflicts are as follows: </p>';
							$errs .=  '<div class="panel error-box">'.$dt.'</div>';
							$errors[] = $errs;
							
							$this->data['error'] = implode('', $errors);
							
							$this->session->set_flashdata('error',implode('', $errors));
							$this->_write_log(print_r($errors, true).'a');
						}
						else
						{
							$this->event_model->save($event);
							
							// send mail
							$mail = $this->input->post('email_notification_4');
							if($mail)
							{
								// send mails
								$this->_send_mail_student($event);
							}
							
							$this->session->set_flashdata('message', 'Event and its schedules has been created.');
							
							if(empty($calendar_view))
							{
								redirect('calendar', 'refresh');
							}
							else
							{
								redirect("calendar?view={$calendar_view['view']}&start={$calendar_view['start']}", 'refresh');
							}
						}
					}
				}
				break;
				
				case 'reschedule':
				if( ! empty($_GET['current_calendar_view']) &&
				! empty($_GET['current_calendar_start'])) {
					$calendar_view = array(
					'view' => $_GET['current_calendar_view'],
					'start' => $_GET['current_calendar_start']
					);
				}
				
				$start = null;
				$end = null;
				$mail = false;
				$whole_event = false;
				
				if( ! empty($_GET['start']))
				{
					$start = $_GET['start'];
				}
				else
				{
					$this->session->set_flashdata('error', 'Lesson cannot be re-scheduled because it does not have start time.');
				}
				
				if( ! empty($_GET['end']))
				{
					$end = $_GET['end'];
				}
				else
				{
					$this->session->set_flashdata('error', 'Lesson cannot be re-scheduled because it does not have end time.');
				}
				
				if( ! empty($_GET['mail']))
				{
					$mail = $_GET['mail'];
				}
				
				if( ! empty($_GET['whole_event']))
				{
					$whole_event = $_GET['whole_event'];
				}
				
				$new_schedules = array();
				$new_schedule = $this->schedule_model->schedules($cid);
				$new_schedule->start = $start;
				$new_schedule->end = $end;
				$new_schedules[] = $new_schedule;
				
				// cannot reschedule into the past
				if($start < time())
				{
					$this->session->set_flashdata('error', 'Lesson cannot be re-scheduled into the past.');
					
					if(empty($calendar_view))
					{
						redirect('calendar', 'refresh');
					}
					else
					{
						redirect("calendar?view={$calendar_view['view']}&start={$calendar_view['start']}", 'refresh');
					}
				}
				
				// get event
				$result = $this->db->query('SELECT * FROM schedules WHERE cid = ?', array($cid))->row();
				$eid = $result->eid;
				$event = $this->event_model->events($eid);
				$new_schedule->event = $event;
				
				$old_schedules = array();
				$final_schedules = array();
				$mails_to_notify = array();
				if($whole_event) {
					$final_schedules[] = $new_schedule; // adds the initial event to the final array
					
					//takes care of all the old schedule data
					$old = $this->db->query('SELECT * FROM schedules WHERE eid = ?', array($eid))->result();
					foreach($old as $old_schedule) {
						$old_schedules[] = $old_schedule;
					}
					
					//returns all of the events but the initial
					$result = $this->db->query('SELECT * FROM schedules WHERE eid = '.$eid.' AND start > '.$start.' AND status IN ('.Schedule_model::STATUS_PENDING . ',' . Schedule_model::STATUS_PAID_PENDING.') AND cid != '.$cid.' ORDER BY start')->result();
					
					/*$log_txt = print_r($this->db->last_query(), true);
						
					$this->_write_log( $log_txt );*/
					
					//takes care of futures events
					foreach($result as $_schedule) {
						$weeks = round(($_schedule->start - $new_schedules[0]->start) / (86400 * 7));
						$_schedule->start = strtotime("+$weeks weeks", $new_schedules[0]->start);
						$_schedule->end = strtotime("+$weeks weeks", $new_schedules[0]->end);
						$_schedule->event = $event;
						
						/*$log_txt = print_r($_schedule, true);
						$this->_write_log( $log_txt );*/
						
						$final_schedules[] = $_schedule;
					}
					//saves ALL of the schedules including future and initial
					$is_sched_valid = true;
					$errors = array();
					$sched_no = '';
					$dt = '';
					
					$new_n = '';
					$last_n = '';
					$sd = '';
					
					foreach($final_schedules as $_schedule) {
						//RESCHED_WHOLE_IS_VALID_2
						$rs = $this->schedule_model->is_valid_2($_schedule);
						
						if(  !empty($rs)  )
						{
							
							$is_sched_valid = false;
							$sched_no .= '#'.$_schedule->sequence.', ';
							
							foreach($rs as $rd => $rk)
							{
								$new_n = $rk->name;
								
								if ( $new_n != $last_n )
								{
									$sd = '<strong>Student: '.$rk->name.'</strong>';
								}
								
								$dt .= '<div class="indent-32">'.'<li>Lesson #'.$rk->sequence.' - '.date("M j, Y", $rk->start);
								$dt .= '<div class="indent-32">Start: '.date("g:i a", $rk->start).'<br>';
								$dt .= 'End: '.date("g:i a", $rk->end).'</div></div></li>';
							}
							
							$dt = $sd.$dt;
							
							$sd = '';
							$last_n = $new_n;
							
						}
					}
					
					if ($is_sched_valid)
					{
						foreach($final_schedules as $_schedule) {
							
							$this->schedule_model->save($_schedule);
							
							$mails_to_notify[] = $_schedule;
						}
						$this->session->set_flashdata('message', 'Lesson(s) have been re-scheduled.');
					}
					else
					{
						$errs = '<p>Unable to proceed. The schedule for lesson(s) ' . rtrim($sched_no, ', ') . ' cannot be re-scheduled because it conflicts another lesson with same teacher.</p>';
						$errs .= '<p>Conflicts are as follows: </p>';
						$errs .=  '<div class="panel error-box">'.$dt.'</div>';
						$errors[] = $errs;
						
						$this->data['error'] = implode('', $errors);
						
						$this->session->set_flashdata('error',implode('', $errors));
					}
					} else {
					$old_schedules[] = $result;
					//RESCHED_SINGLE_IS_VALID_2
					$rs = $this->schedule_model->is_valid_2($new_schedule);
					
					/*if( !empty($rs) ) {*/
						$dt = '';
						
						$new_n = '';
						$last_n = '';
						$sd = '';
						
						foreach($rs as $rd => $rk)
						{
							$new_n = $rk->name;
							
							if ( $new_n != $last_n )
							{
								$sd = '<strong>Student: '.$rk->name.'</strong>';
							}
							
							$dt .= '<div class="indent-32">'.'<li>Lesson #'.$rk->sequence.' - '.date("M j, Y", $rk->start);
							$dt .= '<div class="indent-32">Start: '.date("g:i a", $rk->start).'<br>';
							$dt .= 'End: '.date("g:i a", $rk->end).'</div></div></li>';
						}
						
						/*$dt = $sd.$dt;
						
						$sd = '';
						$last_n = $new_n;
						
						$errs = '<p>Unable to proceed. Lesson #' . $new_schedule->sequence . ' cannot be re-scheduled because it conflicts another lesson with same teacher.</p>';
						$errs .= '<p>Conflicts are as follows: </p>';
						$errs .=  '<div class="panel error-box">'.$dt.'</div>';
						
						$errors[] = $errs;
						
						$this->session->set_flashdata('error',implode('', $errors));*/
					/*}
					else
					{*/
						$this->schedule_model->save($new_schedule);
						$this->session->set_flashdata('message', 'Lesson(s) have been re-scheduled.');
						
						$mails_to_notify[] = $new_schedule;
					}
				/*}*/
				
				// send mail
				if($mail && ! empty($mails_to_notify))
				{
					$event->teacher = $this->user_model->staff_info($new_schedule->staff_id);
					$event->student = $this->user_model->student_info($event->student_id);
					$this->_reschedule_mail_student($event, $mails_to_notify, $old_schedules);
				}
				
				break;
				
				case 'mark-paid':
				if( ! empty($_GET['current_calendar_view']) &&
				! empty($_GET['current_calendar_start'])) {
					$calendar_view = array(
					'view' => $_GET['current_calendar_view'],
					'start' => $_GET['current_calendar_start']
					);
				}
				
				// for this case $cid is $eid
				$eid = $cid;
				$this->event_model->save((object) array(
				'eid' => $eid,
				'status' => Event_model::STATUS_ACTIVE,
				));
				
				$unpaid = $this->db->query('SELECT * FROM schedules WHERE eid = ? AND status = "1"' , array($eid))->result();
				foreach($unpaid as $unpaid_schedule) {
					$cid = $unpaid_schedule->cid;
					$this->schedule_model->save((object) array(
					'cid' => $cid,
					'status' => Schedule_model::STATUS_PAID_PENDING,
					));
				}
				break;
				
				case 'mark-paid-one':
				if( ! empty($_GET['current_calendar_view']) &&
				! empty($_GET['current_calendar_start'])) {
					$calendar_view = array(
					'view' => $_GET['current_calendar_view'],
					'start' => $_GET['current_calendar_start']
					);
				}
				
				$this->schedule_model->save((object) array(
				'cid' => $cid,
				'status' => Schedule_model::STATUS_PAID_PENDING,
				));
				
				// if this is the last schedule paid, mark the entire event as paid
				$schedule_row = $this->db->query('SELECT * FROM schedules WHERE cid = ?', array($cid))->row();
				$eid = $schedule_row->eid;
				$unpaid_count = $this->db->query('SELECT * FROM schedules WHERE eid = ? AND status = "1"', array($eid))->num_rows();
				if ($unpaid_count == 0) {
					$this->event_model->save((object) array(
					'eid' => $eid,
					'status' => Event_model::STATUS_ACTIVE,
					));
				}
				break;
				
				case 'mark-unpaid':
				if( ! empty($_GET['current_calendar_view']) &&
				! empty($_GET['current_calendar_start'])) {
					$calendar_view = array(
					'view' => $_GET['current_calendar_view'],
					'start' => $_GET['current_calendar_start']
					);
				}
				
				// for this case $cid is $eid
				$eid = $cid;
				$this->event_model->save((object) array(
				'eid' => $eid,
				'status' => Event_model::STATUS_UNPAID,
				));
				
				$paid = $this->db->query('SELECT * FROM schedules WHERE eid = ? AND status = "6"' , array($eid))->result();
				foreach($paid as $paid_schedule) {
					$cid = $paid_schedule->cid;
					$this->schedule_model->save((object) array(
					'cid' => $cid,
					'status' => Schedule_model::STATUS_PENDING,
					));
				}
				break;
				
				case 'mark-unpaid-one':
				if( ! empty($_GET['current_calendar_view']) &&
				! empty($_GET['current_calendar_start'])) {
					$calendar_view = array(
					'view' => $_GET['current_calendar_view'],
					'start' => $_GET['current_calendar_start']
					);
				}
				
				$this->schedule_model->save((object) array(
				'cid' => $cid,
				'status' => Schedule_model::STATUS_PENDING,
				));
				
				// if this would mark an entirely paid event with an unpaid schedule, mark the entire event as unpaid
				$schedule_row = $this->db->query('SELECT * FROM schedules WHERE cid = ?', array($cid))->row();
				$eid = $schedule_row->eid;
				$paid_count = $this->db->query('SELECT * FROM schedules WHERE eid = ? AND status = "6"', array($eid))->num_rows();
				if ($paid_count == 0) {
					$this->event_model->save((object) array(
					'eid' => $eid,
					'status' => Event_model::STATUS_UNPAID,
					));
				}
				break;
			}
			
			if(empty($calendar_view))
			{
				redirect('calendar', 'refresh');
			}
			else
			{
				redirect("calendar?view={$calendar_view['view']}&start={$calendar_view['start']}", 'refresh');
			}
		}
		
		private function _prepare_events($schedules)
		{
			// populate events
			$events = array();
			$timezoneOffset = 0;
			
			if( ! empty($schedules))
			{
				foreach($schedules as $i => $schedule)
				{
					$student = $schedule->event->student;
					$product = $schedule->event->product;
					$studio = $schedule->studio;
					$teacher = $schedule->teacher;
					
					// prevent calendar not displaying correctly if those data failed to load
					if(empty($product) || empty($student) || empty($teacher) || empty($studio)) {
						continue;
					}
					
					$lessons = intval($product->lessons);
					$sequence = intval($schedule->sequence);
					
					if($schedule->sequence == 0) {
						$descriptionTitle = $schedule->event->name;
						} else {
						$descriptionTitle = $schedule->event->name . ' <em>(lesson: ' . $schedule->sequence . ' of ' . $lessons . ')</em>';
					}
					
					$description = '<ul class="unstyled">';
					$description .= '<li><strong>Teacher: </strong>';
					$description .= $teacher->first_name . ' ' . $teacher->last_name . ' <em>(' . $teacher->mail . ')</em>';
					$description .= '</li>';
					$description .= '<li><strong>Student: </strong>';
					$description .= $student->first_name . ' ' . $student->last_name . '<br /><em>(e: ' . $student->mail . ', m: ' . $student->mobile . ')</em>';
					$description .= '</li>';
					$description .= '<li><strong>Package: </strong>' . $product->name . ' <em>(' . $product->lessons . ' lessons)</em></li>';
					$description .= '<li><strong>Studio: </strong>' . $studio->name . '</li>';
					$description .= '<li><strong>Status: </strong>' . $this->schedule_model->status($schedule->status) . '</li>';
					$description .= '<li><strong>Description: </strong>' . ($schedule->description ? $this->typography->auto_typography($this->reason_model->reason_value($schedule->description)) : $this->typography->auto_typography($schedule->event->description)) . '</li>';
					$description .= '</ul>';
					
					// calculating color scheme
					$colors = (object)array(
					'back' => self::BACK_DEFAULT,
					'text' => self::TEXT_DEFAULT
					);
					
					// calculate rebook
					$schedule->rebook = FALSE;
					
					switch($lessons - $sequence) {
						case 0: // 1 lesson left: RED background
						$colors = (object)array(
						'back' => self::BACK_ONE_LEFT,
						'text' => self::TEXT_ONE_LEFT
						);
						$schedule->rebook = TRUE;
						break;
						case 1: // 2 lessons left: YELLOW background
						$colors = (object)array(
						'back' => self::BACK_TWO_LEFT,
						'text' => self::TEXT_TWO_LEFT
						);
						break;
						case ($lessons - 1): // first lesson: BLUE background
						$colors = (object)array(
						'back' => self::BACK_FIRST_LESSON,
						'text' => self::TEXT_FIRST_LESSON
						);
						break;
					}
					
					// checking unpaid event
					if($schedule->status == Schedule_model::STATUS_PENDING || $schedule->status == Schedule_model::STATUS_UNPAID_PASSED)
					{
						$colors = (object)array(
						'back' => self::BACK_FIRST_LESSON_UNPAID,
						'text' => self::TEXT_FIRST_LESSON_UNPAID,
						);
						$schedule->unpaid = TRUE;
					}
					
					// flagging paid event
					if($schedule->status == Schedule_model::STATUS_PAID_PENDING) {
						$schedule->paid = TRUE;
					}
					
					if($schedule->status == Schedule_model::STATUS_CANCELED_OVER_24)
					{
						$colors = (object)array(
						'back' => self::BACK_CANCELED_OVER_24,
						'text' => self::TEXT_CANCELED_OVER_24
						);
					}
					
					if($schedule->status == Schedule_model::STATUS_CANCELED_IN_24)
					{
						$colors = (object)array(
						'back' => self::BACK_CANCELED_UNDER_24,
						'text' => self::TEXT_CANCELED_UNDER_24
						);
					}
					
					if($schedule->status == Schedule_model::STATUS_PAID_PENDING && ($lessons - $sequence) > 1 && ($lessons - $sequence) < $lessons - 1 )
					{
						$colors = (object)array(
						'back' => self::BACK_GREEN_LESSON_PAID,
						'text' => self::TEXT_GREEN_LESSON_PAID
						);
					}
					
					/*if ( $schedule->event->name == 'EZYVA' )
						{
						$log_txt = 'EVENT NAME: '.$schedule->event->name.' | ID: '.$schedule->event->eid."\n".$student->first_name . ' ' . $student->last_name."\n";
						$log_txt .= 'LESSONS: '.$lessons."\nSEQUENCE:".$sequence."\nRESULT:".($lessons - $sequence);
						$this->_write_log( $log_txt );
					}*/
					// calculating last schedule in the event, using for CANCEL OVER 24
					$result = $this->db->query("SELECT start FROM schedules WHERE eid = ? ORDER BY cid DESC LIMIT 0, 1", array(
					$schedule->event->eid
					))->row();
					$schedule->calculated_cancel_over_24_time = strtotime('+1 week', $result->start);
					
					// note history
					$schedule->note_history = $this->note_model->note_history($student->sid);
					$event = (object)array(
					'id' => $schedule->cid,
					'title' => $schedule->event->name,
					'start' => $schedule->start + $timezoneOffset,
					'end' => $schedule->end + $timezoneOffset,
					'allDay' => false,
					'className' => array('event-popover'),
					'description' => '<small><h6>' . $descriptionTitle . '</h6>' . $description . '</small>',
					'schedule' => $schedule,
					'textColor' => $colors->text,
					'backgroundColor' => $colors->back,
					'borderColor' => '#333333',
					'editable' => ($schedule->status == Schedule_model::STATUS_PENDING || $schedule->status == Schedule_model::STATUS_PAID_PENDING) ? TRUE : FALSE,
					//'editable' => $schedule->status == Schedule_model::STATUS_PENDING ? TRUE : FALSE,
					);
					
					$events[] = $event;
				}
			}
			
			return $events;
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
		private function _delete_mail_student($event)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $event->student;
			$teacher = $event->teacher;
			$product = $event->product;
			
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "SCHEDULE REMOVED | Vox Singing Academy",
			'message' => "
			<p>Hi {$student->first_name},</p>
			<p>The following session has been removed:</p>
			<p><strong>Session: </strong> {$product->name}</p>
			<p><strong>Reason: </strong>" . $this->delete_reason_model->reason_value($event->description) . "</p>
			<p>If you require further assistance, please call us on <strong>1300 183 732</strong>.  We look forward to seeing you soon and remember to have FUN singing!</p>
			<p>Thank you!</p>
			<p><strong>Vox Singing Academy | Australia's Voice Training Specialist</strong></p>
			"
			)
			);
			
			$admin_check = $this->user_model->has_role($this->user->uid, '1');
			if ($admin_check == TRUE) {
				my_send_mail(
				array(
				'to' => $teacher->mail,
				'subject' => "ADMIN: SCHEDULE REMOVED | Vox Singing Academy",
				'message' => "
				<p>Hi {$teacher->first_name},</p>
				<p>The following session has been removed:</p>
				<p><strong>Session: </strong> {$product->name}</p>
				<p><strong>Reason: </strong>" . $this->delete_reason_model->reason_value($event->description) . "</p>
				<p>Thank you!</p>
				<p><strong>Vox Singing Academy | Australia's Voice Training Specialist</strong></p>
				"
				)
				);
			}
		}
		
		private function _reschedule_mail_student($event, $new_schedules, $old_schedules)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $event->student;
			$teacher = $event->teacher;
			
			$output_old = '';
			$output_new = '';
			
			foreach($old_schedules as $old_schedule)
			{
				$output_old .= "
				<p><strong>Start: " . date('d/m/Y g:ia', $old_schedule->start) . " - End: " . date('d/m/Y g:ia', $old_schedule->end) . "</strong></p>
				";
			}
			
			foreach($new_schedules as $new_schedule)
			{
				$output_new .= "
				<p><strong>Start: " . date('d/m/Y g:ia', $new_schedule->start) . " - End: " . date('d/m/Y g:ia', $new_schedule->end) . "</strong></p>
				";
			}
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "RESCHEDULED | Vox Singing Academy",
			'message' => "<p>Hi {$student->first_name},</p>
			<p>The following session has been rescheduled:</p>
			{$output_old}
			<p>The new session has been rescheduled for the following time:</p>
			{$output_new}
			<p>If you require further assistance, please call us on <strong>1300 183 732</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
			<p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			"
			)
			);
			
			// $admin_check = $this->user_model->has_role($this->user->uid, '1');
			// if ($admin_check == TRUE) {
			//   my_send_mail(
			//     array(
			//       'to' => $teacher->mail,
			//       'subject' => "ADMIN: RESCHEDULED | Vox Singing Academy",
			//       'message' => "<p>Hi {$teacher->first_name},</p>
			//         <p>The following session has been rescheduled:</p>
			//         {$output_old}
			//         <p>The new session has been rescheduled for the following time:</p>
			//         {$output_new}
			//         <p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			//         "
			//     )
			//   );
			// }
		}
		
		private function _cancel_mail_student($event, $old_schedule, $new_schedule)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $event->student;
			$teacher = $event->teacher;
			
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "CANCELLATION NOTIFICATION | Vox Singing Academy",
			'message' => "
			<p>Hi {$student->first_name},</p>
			<p>Thank you for letting us know.</p>
			<p>You have not been charged for the following cancellation:</p>
			<p><strong>Start: " . date('d/m/Y g:ia', $old_schedule->start) . " - End: " . date('d/m/Y g:ia', $old_schedule->end) . "</strong></p>
			<p>Your new lesson as been rescheduled for following time:<br />
			<strong>Start: " . date('d/m/Y g:ia', $new_schedule->start) . " - End: " . date('d/m/Y g:ia', $new_schedule->end) . "</strong></p>
			<p><strong>Reason: </strong>" . $this->reason_model->reason_value($old_schedule->description) . "</p>
			<p>If you require further assistance, please call us on <strong>1300 183 732</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
			<p>Thank you, </p>
			<p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			"
			)
			);
			
			// $admin_check = $this->user_model->has_role($this->user->uid, '1');
			// if ($admin_check == TRUE) {
			//   my_send_mail(
			//     array(
			//       'to' => $teacher->mail,
			//       'subject' => "ADMIN: CANCELLATION NOTIFICATION | Vox Singing Academy",
			//       'message' => "
			//       <p>Hi {$teacher->first_name},</p>
			//       <p>The following lesson has been cancelled:</p>
			//       <p><strong>Start: " . date('d/m/Y g:ia', $old_schedule->start) . " - End: " . date('d/m/Y g:ia', $old_schedule->end) . "</strong></p>
			//       <p>The new lesson as been rescheduled for following time:<br />
			//       <strong>Start: " . date('d/m/Y g:ia', $new_schedule->start) . " - End: " . date('d/m/Y g:ia', $new_schedule->end) . "</strong></p>
			//       <p><strong>Reason: </strong>" . $this->reason_model->reason_value($old_schedule->description) . "</p>
			//       <p>This is an automated notification from VoxCRM. Please do not respond.</p>
			//         "
			//     )
			//   );
			// }
		}
		
		private function _cancel_under24_mail_student($event, $schedule)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $event->student;
			$teacher = $event->teacher;
			
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "CANCELLATION NOTIFICATION | Vox Singing Academy",
			'message' => "
			<p>Hi {$student->first_name},</p>
			<p>This is a confirmation that you have cancelled under 24 hour notice.<p>
			<p><strong>Reason: </strong>" . $this->reason_model->reason_value($schedule->description) . "
			</p>
			<p>If you require further assistance, please call us on <strong>1300 183 732</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
			<p>Thank you,</p>
			<p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			"
			)
			);
			
			// $admin_check = $this->user_model->has_role($this->user->uid, '1');
			// if ($admin_check == TRUE) {
			//   my_send_mail(
			//     array(
			//       'to' => $teacher->mail,
			//       'subject' => "ADMIN: CANCELLATION NOTIFICATION | Vox Singing Academy",
			//       'message' => "
			//       <p>Hi {$teacher->first_name},</p>
			//       <p>This is a confirmation that you have a cancelled appointment under 24 hour notice.<p>
			//       <p><strong>Reason: </strong>" . $this->reason_model->reason_value($schedule->description) . "
			//       </p>
			//       <p>This is an automated notification from VoxCRM. Please do not respond.</p>
			//         "
			//     )
			//   );
			// }
		}
		
		private function _send_mail_student($event)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $event->student;
			$product = $event->product;
			
			$studio = NULL;
			$teacher = NULL;
			
			$event_output = '';
			foreach($event->schedules as $i => $schedule)
			{
				if($i == 0) {
					$studio = $this->studio_model->studios($schedule->studio_id);
					$teacher = $this->user_model->staff_info($schedule->staff_id);
				}
				
				$event_output .= "
				<li>
				<h5>Session {$schedule->sequence}</h5>
				<span><strong>Start: </strong><em>" . date('d/m/Y g:ia', $schedule->start) . "</em> -
				<strong>End: </strong><em>" . date('d/m/Y g:ia', $schedule->end) . "</em></span>
				</li>
				";
			}
			
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "RESERVATION | Vox Singing Academy",
			'message' => "
			<p>Hi {$student->first_name},</p>
			<p>Thank you for your enquiry and booking for a private singing lessons at Vox Singing Academy.<br />
			Please check the details of your reservation:</p>
			<p><strong>Session</strong>: {$product->name}</p>
			<p><strong>Status</strong>: Pending</p>
			<p><strong>Studio</strong>: {$studio->street} {$studio->city} {$studio->postcode}</p>
			<p><strong>Teacher</strong>: {$teacher->first_name} {$teacher->last_name}</p>
			<p><strong>Mobile</strong>: {$teacher->mobile}</p>
			
			<p>
			<h4>Sessions:</h4>
			<ul>$event_output</ul>
			</p>
			
			<p>After the initial student induction lesson, all further tuition MUST be paid with the corresponding terms.</p>
			
			<p><strong>5 lesson term package</strong></p>
			<ul>
			<li><strong>Standard:</strong> $200 ($40 lessons)</li>
			<li><strong>Peter Vox:</strong> $250 ($50 lessons)</li>
			<li><strong>Peter Vox Premium:</strong> $325 ($65 lessons)</li>
			</ul>
			
			<p><strong>10 lesson term package,</strong> and get <strong>1 FREE</strong> (BEST VALUE)</p>
			<ul>
			<li><strong>Standard:</strong> $360 ($40 lessons)</li>
			<li><strong>Peter Vox:</strong> $450 ($50 lessons)</li>
			<li><strong>Peter Vox Premium:</strong> $585 ($65 lessons)</li>
			</ul>
			
			<ul>
			<li>All lessons MUST be pre-paid.</li>
			<li>Initial bookings must be pre-paid to secure your time slot.</li>
			<li>Renewal of term payment must be received 48 hours after the completion of your last lesson of your tern to secure your time slot.</li>
			<li>You must notify your teacher 24 hours prior to your lesson if your circumstances change and cannot attend your lesson.</li>  
			<li>Cancellations under 24-hour will result in loss of fee.</li>
			</ul>
			
			<p>
			<h5>Payment options:</h5>
			<ul>
			<li>Direct Deposit</li>
			<li>Electronic Funds Transfer</li>
			<li>Credit Card</li>
			<li>Cash payments are strictly not accepted.</li>
			</ul>
			
			<p>Please make payment to:</p>
			<p><strong>Commonwealth Bank</strong></p>
			<table border='1' cellpadding='4'>
			<tr>
			<td><strong>Account name:</strong></td>
			<td>A Vox Music Academy</td>
			</tr>
			<tr>
			<td><strong>BSB:</strong></td>
			<td>063-595</td>
			</tr>
			<tr>
			<td><strong>Account No:</strong></td>
			<td>1058-2208</td>
			</tr>
			<tr>
			<td><strong>Reference:</strong></td>
			<td>{$student->first_name} {$student->last_name}</td>
			</tr>
			</table>
			</p>
			<p>
			<h5>Please bring to all singing lessons:</h5>
			<ul>
			<li>Your song choice with original artists on your smartphone, iPod or CD.</li>
			<li>Lyrics printed or written out for your teacher.</li>
			<li>A mobile recording device. </li>
			<li>Exercise book for notes.</li>
			<li>A bottle of room temperature water.</li>
			</ul>
			If you require further assistance, please call us on <strong>1300 183 732</strong>.  We look forward to seeing you soon and remember to have FUN singing!</p>
			<p>Warm regards,</p>
			<p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			"
			)
			);
			
			$admin_check = $this->user_model->has_role($this->user->uid, '1');
			if ($admin_check == TRUE) {
				my_send_mail(
				array(
				'to' => $teacher->mail,
				'subject' => "ADMIN: RESERVATION | Vox Singing Academy",
				'message' => "
				<p>Hi {$teacher->first_name},</p>
				<p>This is an email to notify you of new singing lesson reservations.</p>
				
				<p>Please check the details of the reservation:</p>
				<p><strong>Session</strong>: {$product->name}</p>
				<p><strong>Status</strong>: Pending</p>
				<p><strong>Studio</strong>: {$studio->street} {$studio->city} {$studio->postcode}</p>
				<p><strong>Student</strong>: {$student->first_name} {$student->last_name}</p>
				<p><strong>Email</strong>: {$student->mail}</p>
				<p><strong>Mobile</strong>: {$student->mobile}</p>
				
				<p>
				<h4>Sessions:</h4>
				<ul>$event_output</ul>
				</p>
				
				<p>This is an automated notification from VoxCRM. Please do not respond.</p>
				"
				)
				);
			}
		}
		
		private function _edit_lessons_send_mail_student($event, $edited_schedules)
		{
			$voxcrm_mail = $this->config->item('voxcrm_mail');
			$student = $this->user_model->student_info($event->student_id);
			
			$event_output = '';
			foreach($edited_schedules as $schedule)
			{
				$studio = $this->studio_model->studios($schedule->studio_id);
				$teacher = $this->user_model->staff_info($schedule->staff_id);
				$event_output .= "
				<li>
				<h3>Lesson {$schedule->sequence}</h3>
				<span><strong>Start: <em>" . date('d/m/Y g:ia', $schedule->start) . "</em> -
				End: </strong>" . date('d/m/Y g:ia', $schedule->end) . "</em></strong></span><br />
				<strong>Studio: </strong> {$studio->street} {$studio->city} {$studio->postcode}<br />
				<strong>Teacher: </strong> {$teacher->first_name} {$teacher->last_name}<br />
				<strong>Mobile: </strong> {$teacher->mobile}
				</li>
				";
			}
			
			my_send_mail(
			array(
			'to' => $student->mail,
			'subject' => "LESSON UPDATE | Vox Singing Academy",
			'message' => "
			<p>Hi {$student->first_name},</p>
			<p>The following session(s) have been updated:</p>
			<ul>$event_output</ul>
			<p>If you require further assistance, please call us on <strong>1300 183 732</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
			<p>Thank you, </p>
			<p><strong>Vox Singing Academy | Australia’s Voice Training Specialist</strong></p>
			"
			)
			);
			
			 $admin_check = $this->user_model->has_role($this->user->uid, '1');
			 if ($admin_check == TRUE) {
			   my_send_mail(
			     array(
			       'to' => $teacher->mail,
			       'subject' => "ADMIN: LESSON UPDATE | Vox Singing Academy",
			       'message' => "
			       <p>Hi {$teacher->first_name},</p>
			       <p>The following session(s) have been updated:</p>
			       <ul>$event_output</ul>
			       <p>This is an automated notification from VoxCRM. Please do not respond.</p>
			         "
			     )
			   );
			 }
			
		}
	}
