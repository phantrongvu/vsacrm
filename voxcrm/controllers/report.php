<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends MY_Controller
{
  function __construct()
  {
    parent::__construct();



    $this->load->model('user_model');
    $this->load->model('report_model');
    $this->load->model('schedule_model');
    $this->load->model('reason_model');
    $this->load->model('email_admin_reason');
    $this->load->model('delete_reason_model');
	$this->load->model('log_report_model');
  }

  function timesheet($uid = NULL)
  {
    $week = $this->input->get('week');

    if(empty($week)) {
      $week = 0;
    }

    $start = strtotime('monday last week');
    $end = strtotime('monday this week');

    $start = $start + (86400 * 7 * $week);
    $end = $end + (86400 * 7 * $week);

    if( ! empty($uid))
    {
      $schedules = $this->report_model->timesheet($uid, $start, $end);
      $teacher = $this->user_model->staff_info($uid);

      // separate status
      $passes = array();
      $over24 = array();
      $under24 = array();
      $unpaid = array();
      $custom_products = array();

      $passes_count = 0;
      $over24_count = 0;
      $under24_count = 0;
      $unpaid_count = 0;

      $unit = 30 * 60;  // 30 mins

      foreach($schedules as $schedule)
      {
        // don't include BREAK or RESERVED
        if(in_array($schedule->name, array('BREAK', 'RESERVED')))
        {
          continue;
        }

        // only calculate product that has 'singing lesson' in the name
        if(strpos(strtolower($schedule->product_name), 'singing lesson') === FALSE) {
          $custom_products[date('l', $schedule->start)][] = $schedule;
          continue;
        }

        // if($schedule->event_status == Event_model::STATUS_UNPAID)
        // {
        //   $unpaid[date('l', $schedule->start)][] = $schedule;
        //   $unpaid_count += (int) (($schedule->end - $schedule->start) / $unit);
        // }
        // else
        // {
        //   switch($schedule->status) {
        //     case Schedule_model::STATUS_PASSED:
        //       $passes[date('l', $schedule->start)][] = $schedule;
        //       $passes_count += (int) (($schedule->end - $schedule->start) / $unit);
        //       break;

        //     case Schedule_model::STATUS_CANCELED_OVER_24:
        //       $over24[date('l', $schedule->start)][] = $schedule;
        //       $over24_count += (int) (($schedule->end - $schedule->start) / $unit);
        //       break;

        //     case Schedule_model::STATUS_CANCELED_IN_24:
        //       $under24[date('l', $schedule->start)][] = $schedule;
        //       $under24_count += (int) (($schedule->end - $schedule->start) / $unit);
        //       break;
        //   }
        // }
        switch($schedule->status) {
          //unpaid
          case Schedule_model::STATUS_PENDING:
            $unpaid[date('l', $schedule->start)][] = $schedule;
            $unpaid_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;

          case Schedule_model::STATUS_UNPAID_PASSED:
            $unpaid[date('l', $schedule->start)][] = $schedule;
            $unpaid_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;

          //paid or passed
          case Schedule_model::STATUS_PASSED:
            $passes[date('l', $schedule->start)][] = $schedule;
            $passes_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;

          case Schedule_model::STATUS_PAID_PENDING:
            $passes[date('l', $schedule->start)][] = $schedule;
            $passes_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;
        
          //cancelled
          case Schedule_model::STATUS_CANCELED_OVER_24:
            $over24[date('l', $schedule->start)][] = $schedule;
            $over24_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;

          case Schedule_model::STATUS_CANCELED_IN_24:
            $under24[date('l', $schedule->start)][] = $schedule;
            $under24_count += (int) (($schedule->end - $schedule->start) / $unit);
            break;
        }
      }

      $this->data += array(
        'teacher' => $teacher,
        'schedules' => array(
          'passes' => $passes,
          'over24' => $over24,
          'under24' => $under24,
          'unpaid' => $unpaid,
        ),
        'count' => array(
          'passes' => $passes_count,
          'over24' => $over24_count,
          'under24' => $under24_count,
          'unpaid' => $unpaid_count,
        ),
        'custom_products' => $custom_products,
        'week' => $week,
      );
    }

    $this->data += array(
      'title' => 'Timesheet',
      'template' => 'timesheet',
      'range' => array(
        'start' => $start,
        'end' => $end
      ),
      'post' => $_POST
    );
    $this->add_asset('js/timesheet.js?20130514');
    $this->add_asset('css/timesheet.css', 'css');
    $this->render_page();
  }

  function teacher_performance()
  {
    $uid = $this->input->get('uid');
    $start = $this->input->get('start');
    $end = $this->input->get('end');
    $view = $this->input->get('view');  // weekly, monthly

    if(empty($start) && empty($end))
    {
      $start = strtotime('-1 month');
      $end = strtotime('now');
    }

    if(empty($view) || $view == 'undefined')
    {
      $view = 'week';
    }

    if( ! empty($uid))
    {
      // separate status
      $passes = array();
      $over24 = array();
      $under24 = array();

      $passes_data = array();
      $over24_data = array();
      $under24_data = array();

      $unit = 30 * 60;  // 30 mins

      $schedules = $this->report_model->teacher_performance($uid, $start, $end);
      $key = '';

      foreach($schedules as $i => $schedule)
      {
        // don't include BREAK or RESERVED
        if(in_array($schedule->name, array('BREAK', 'RESERVED')))
        {
          unset($schedules[$i]);
          continue;
        }

        // only calculate product that has 'singing lesson' in the name
        if(strpos(strtolower($schedule->product_name), 'singing lesson') === FALSE) {
          unset($schedules[$i]);
          continue;
        }

        switch($view)
        {
          case 'week':
            $key = date('d/m/y', shift_to_day_of_week($schedule->start, date('N', $start)));
            break;

          case 'month':
            $key = date('m/y', $schedule->start);
            break;

          default:
            $key = date('d/m/y', shift_to_day_of_week($schedule->start, date('N', $start)));
            break;
        }

        switch($schedule->status)
        {
          case Schedule_model::STATUS_PASSED:
            $passes[$key][] = $schedule;
            break;

          case Schedule_model::STATUS_CANCELED_OVER_24:
            $over24[$key][] = $schedule;
            break;

          case Schedule_model::STATUS_CANCELED_IN_24:
            $under24[$key][] = $schedule;
            break;
        }
      }

      // calculating count
      foreach($passes as $key => $_schedules)
      {
        $count = 0;
        foreach($_schedules as $schedule)
        {
          $count += (int) (($schedule->end - $schedule->start) / $unit);
        }

        $passes_data[] = array($key, $count);
      }

      $over24_count_data = array();
      foreach($over24 as $key => $_schedules)
      {
        $count = 0;
        foreach($_schedules as $schedule)
        {
          $count += (int) (($schedule->end - $schedule->start) / $unit);
          if( ! isset($over24_count_data[$key]))
          {
            $over24_count_data[$key] = array();
          }
          if( ! isset($over24_count_data[$key][$schedule->description]))
          {
            $over24_count_data[$key][$schedule->description] = 0;
          }

          $over24_count_data[$key][$schedule->description] += (int) (($schedule->end - $schedule->start) / $unit);
        }
        $over24_data[] = array($key, $count);
      }

      $under24_count_data = array();
      foreach($under24 as $key => $_schedules)
      {
        $count = 0;
        foreach($_schedules as $schedule)
        {
          $count += (int) (($schedule->end - $schedule->start) / $unit);
          if( ! isset($under24_count_data[$key]))
          {
            $under24_count_data[$key] = array();
          }
          if( ! isset($under24_count_data[$key][$schedule->description]))
          {
            $under24_count_data[$key][$schedule->description] = 0;
          }

          $under24_count_data[$key][$schedule->description] += (int) (($schedule->end - $schedule->start) / $unit);
        }

        $under24_data[] = array($key, $count);
      }

      // calculating Retention and Turnover rate
      $last_schedules = array();
      $first_schedules = array();
      foreach($schedules as $schedule)
      {
        if($schedule->lessons == $schedule->sequence)
        {
          $last_schedules[] = $schedule;
        }

        if($schedule->sequence == 1)
        {
          $first_schedules[] = $schedule;
        }
      }

      // search through last schedules, detect first schedule with same student id
      $retention_count = 0;
      foreach($last_schedules as $i => $last_schedule)
      {
        foreach($first_schedules as $j => $first_schedule)
        {
          if($last_schedule->student_id == $first_schedule->student_id &&
            $first_schedule->start > $last_schedule->start)
          {
            // take this schedule out to prevent repeat count
            unset($first_schedules[$j]);
            $retention_count++;
            continue;
          }
        }
      }
      $turnover_count = count($last_schedules) - $retention_count;

      // build up DataTable
      $dataTable_pass = array_merge(
        array(array('Time', 'Lessons')),
        $passes_data
      );

      $dataTable_o24 = array_merge(
        array(array('Time', 'Over 24')),
        $over24_data
      );

      $dataTable_u24 = array_merge(
        array(array('Time', 'Under 24')),
        $under24_data
      );

      // pass DataTable to js
      $this->add_asset('$.extend(VSACRM, {
        DataTable: {
          pass: ' . json_encode($dataTable_pass) . ',
          o24: ' . json_encode($dataTable_o24) . ',
          u24: ' . json_encode($dataTable_u24) . ',
          retention_turnover: {
            retention: ' . $retention_count . ',
            turnover: '. $turnover_count . '
          }
        }
      });', 'inline_js');

      // passing additional data
      $this->add_asset('$.extend(VSACRM, {
        cancellation_reasons: ' . json_encode($this->reason_model->reason_options()) . ',
        o24_count_data: ' . json_encode($over24_count_data) . ',
        u24_count_data: ' . json_encode($under24_count_data) . '
      });', 'inline_js');

      $this->add_asset('js/teacher_performance_charts.js');
    }

    // email admin data
    $email_admins = array();
    if( ! empty($uid))
    {
      $_email_admins = $this->report_model->email_admin($uid, $start, $end);
      foreach($_email_admins as $email_admin)
      {
        $email_admins[$email_admin->reason][] = $email_admin;
      }
    }

    $this->data += array(
      'title' => 'Teacher performance',
      'template' => 'teacher_performance',
      'range' => array(
        'start' => $start,
        'end' => $end
      ),
      'view' => $view,
      'email_admins' => $email_admins,
    );

    // add css
    $this->add_asset('js/vendor/datepicker/css/datepicker.css', 'css');
    $this->add_asset('css/teacher_performance.css', 'css');

    // inline js
    $this->add_asset('$.extend(VSACRM, {
      start: ' . $start . ',
      end: ' . $end . ',
      view: \'' . $view. '\'});', 'inline_js');

    // add js
    $this->add_asset('https://www.google.com/jsapi', 'external_js');
    $this->add_asset('js/vendor/datepicker/js/datepicker.js');
    $this->add_asset('js/vendor/datepicker/js/eye.js');
    $this->add_asset('js/teacher_performance.js');

    $this->render_page();
  }

function log_reports()
	{		
		$date = $this->load->helper('date');
		$forms = $this->load->helper('form');
		if(isset($_POST['save_report']))
		{		
		
			$data['student_data'] = (isset($_POST['student_data'])?1:0);	
			$data['bpoints'] = (isset($_POST['bpoints'])?1:0);
			$data['pays'] = (isset($_POST['pays'])?1:0);
			$data['report'] = (isset($_POST['report'])?1:0);
			$data['crm_updated'] = (isset($_POST['crm_updated'])?1:0);
			$data['phones'] = (isset($_POST['phones'])?1:0);
			$data['emails'] = (isset($_POST['emails'])?1:0);
			$data['return_calls'] = (isset($_POST['return_calls'])?1:0);
			$data['banking'] = (isset($_POST['banking'])?1:0);
			$data['check_back_end_emails'] = (isset($_POST['check_back_end_emails'])?1:0);
			$data['test_website_inquiry_forms'] = (isset($_POST['test_website_inquiry_forms'])?1:0);
			$total = $this->log_report_model->checkLogData(date('Y-m-d'));
			if($total>0)
				$insert_id = $this->log_report_model->updateLogData($data,date("Y-m-d"));
			else{
				$data['log_date'] = date('Y-m-d');
				$insert_id = $this->log_report_model->insertLogData($data);
			}
redirect('report/log_reports');
		}		
		// email admin data
		$email_admins = array();
		if( ! empty($uid))
		{
		  $_email_admins = $this->report_model->email_admin($uid, $start, $end);
		  foreach($_email_admins as $email_admin)
		  {
			$email_admins[$email_admin->reason][] = $email_admin;
		  }
		}

		$this->data += array(
		  'title' => 'Log Reports',
		  'template' => 'log_reports',
		  
		  'email_admins' => $email_admins,
		  'result' =>$this->log_report_model->selectLogData(),
		);
	
		// add css
		$this->add_asset('js/vendor/datepicker/css/datepicker.css', 'css');
		$this->add_asset('css/teacher_performance.css', 'css');
	
		// add js
		$this->add_asset('https://www.google.com/jsapi', 'external_js');
		$this->add_asset('js/vendor/datepicker/js/datepicker.js');
		$this->add_asset('js/vendor/datepicker/js/eye.js');
		$this->add_asset('js/teacher_performance.js');
	
		$this->render_page();
  }

  function sale_performance()
  {
    $start = $this->input->get('start');
    $end = $this->input->get('end');

    if(empty($start) && empty($end))
    {
      $start = strtotime('-1 month');
      $end = strtotime('now');
    }

    $sales_pending = $this->report_model->sale_performance($start, $end, 'pending');
    $sales_won = $this->report_model->sale_performance($start, $end, 'won');
    $_sales_lost = $this->report_model->sale_performance($start, $end, 'lost');

    $sales_lost = array();
    foreach($_sales_lost as $sale_lost)
    {
      $sales_lost[$sale_lost->description][] = $sale_lost;
    }

    $this->data += array(
      'title' => 'Sale performance',
      'template' => 'sale_performance',
      'data' => array(
        'sales_pending' => $sales_pending[0]->cnt,
        'sales_won' => $sales_won[0]->cnt,
        'sales_lost' => $sales_lost
      )
    );

    // add css
    $this->add_asset('js/vendor/datepicker/css/datepicker.css', 'css');
    $this->add_asset('css/sale_performance.css', 'css');

    // inline js
    $this->add_asset('$.extend(VSACRM, {
      start: ' . $start . ',
      end: ' . $end . '});', 'inline_js');

    $this->add_asset('js/vendor/datepicker/js/datepicker.js');
    $this->add_asset('js/vendor/datepicker/js/eye.js');
    $this->add_asset('js/sale_performance.js');

    $this->render_page();
  }

  function sale_forecast()
  {
    $start = $this->input->get('start');
    $end = $this->input->get('end');

    if(empty($start) && empty($end))
    {
      $start = strtotime('-1 month');
      $end = strtotime('now');
    }

    $sale_forecast = $this->report_model->sale_forecast($start, $end);

    $this->data += array(
      'title' => 'Sale forecast',
      'start' => $start,
      'end' => $end,
      'template' => 'sale_forecast',
      'sale_forecast' => $sale_forecast[0]->total,
    );

    // add css
    $this->add_asset('js/vendor/datepicker/css/datepicker.css', 'css');
    $this->add_asset('css/sale_performance.css', 'css');

    // inline js
    $this->add_asset('$.extend(VSACRM, {
      start: ' . $start . ',
      end: ' . $end . '});', 'inline_js');

    $this->add_asset('js/vendor/datepicker/js/datepicker.js');
    $this->add_asset('js/vendor/datepicker/js/eye.js');
    $this->add_asset('js/sale_forecast.js');

    $this->render_page();
  }
}

/* End of file report.php */
/* Location: ./application/controllers/report.php */