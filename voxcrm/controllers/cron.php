<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function _cron_custom_compare($c1, $c2)
{
  return $c1->start > $c2->start;
}

class Cron extends CI_Controller
{

  function __construct()
  {
    parent::__construct();

    $this->load->model('event_model');
    $this->load->model('schedule_model');
    $this->load->model('user_model');

    date_default_timezone_set('Australia/Victoria');
  }

  function run($seret = '')
  {
    if($seret !== 'v8Nj3KO11oA75uO')
    {
      show_error('Access denied');
    }

    // cleaning up events + schedules that doesn't have products or location associated to them
    $sql = "DELETE FROM schedules
WHERE eid IN (SELECT eid FROM events e
WHERE e.product_id NOT IN (SELECT pid FROM products))";
    $this->db->query($sql);
    $this->db->query("DELETE FROM events WHERE product_id NOT IN (SELECT pid FROM products)");

    //cleaning up events + schedules that doesn't have students or teachers associated to theme
    $sql = "DELETE FROM schedules WHERE eid IN (SELECT eid FROM events e WHERE e.student_id NOT IN (SELECT sid FROM students))";
    $this->db->query($sql);
    $this->db->query("DELETE FROM events WHERE student_id NOT IN (SELECT sid FROM students)");

	// set all passed paid schedules to passed
	$sql = "
	UPDATE schedules s SET s.status = ?
	WHERE s.end <= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 72 HOUR)) AND s.status = ?
	";
	$this->db->query($sql, array(
	Schedule_model::STATUS_PASSED,
	Schedule_model::STATUS_PAID_PENDING,
	));
	
	// set all passed unpaid schedules to passed
	$sql = "
	UPDATE schedules s SET s.status = ?
	WHERE s.end <= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 72 HOUR)) AND s.status = ?
	";
	$this->db->query($sql, array(
	Schedule_model::STATUS_UNPAID_PASSED,
	Schedule_model::STATUS_PENDING,
	));
	/*
    // set all passed paid schedules to passed
    $sql = "
      UPDATE schedules s SET s.status = ?
      WHERE s.end <= ? AND s.status = ?
    ";
    $this->db->query($sql, array(
      Schedule_model::STATUS_PASSED,
      time(),
      Schedule_model::STATUS_PAID_PENDING,
    ));

    // set all passed unpaid schedules to passed
    $sql = "
      UPDATE schedules s SET s.status = ?
      WHERE s.end <= ? AND s.status = ?
    ";
    $this->db->query($sql, array(
      Schedule_model::STATUS_UNPAID_PASSED,
      time(),
      Schedule_model::STATUS_PENDING,
    ));*/

    // set all events to inactive if all schedules passed
    $event_ids = array();
    $sql = "
      SELECT e.eid, (
        SELECT COUNT(*)
        FROM schedules AS s
        WHERE s.eid = e.eid
      ) AS cnt, (
        SELECT COUNT(*)
        FROM schedules AS s
        WHERE s.eid = e.eid AND (s.status != " . Schedule_model::STATUS_PENDING . " OR s.status != " . Schedule_model::STATUS_PAID_PENDING . ")
      ) AS cnt1
      FROM events as e
      WHERE (e.status = " . Event_model::STATUS_ACTIVE . " OR e.status = " . Event_model::STATUS_UNPAID . ")
    ";
    $result = $this->db->query($sql)->result();
    foreach($result as $row)
    {
      /*if($row->cnt == $row->cnt1)
      {
        $this->db->query("UPDATE events e SET e.status = ? WHERE e.eid = ?", array(
          Event_model::STATUS_INACTIVE, $row->eid
        ));
      }*/
      $event_ids[] = $row->eid;
    }

    // process sending emails, 11AM every morning (set in config file)
    //$current_hour = date('G');
    //if($current_hour == $this->config->item('voxcrm_mail_time'))
    //{
     // $voxcrm_mail = $this->config->item('voxcrm_mail');

      foreach($event_ids as $eid)
      {
        $event = $this->event_model->events($eid);
        $event->schedules = $this->schedule_model->event_schedules($eid);

        usort($event->schedules, "_cron_custom_compare");

        // second last emails
        $second_last_index = count($event->schedules) - 2 >= 0 ? count($event->schedules) - 2 : 0;
        $second_last_schedule = $event->schedules[$second_last_index];

		$status_valid = false;
		
		if ($second_last_schedule->status == 6 )
			$status_valid = true;
			
        if($second_last_schedule->start > strtotime('0:00') && $second_last_schedule->start < strtotime('0:00') + 86400 && $status_valid === true )
        {
          $student = $this->user_model->student_info($event->student_id);

          my_send_mail(
            array(
              'to' => $student->mail,
              'subject' => "COURTESY REMINDER | Vox Singing Academy",
              'message' => "
<p>Hi {$student->first_name},</p>
<p>Just a courtesy reminder that you have 2 singing lessons remaining before the end of your term. To secure your time slot, please ensure your payment is received 48 hours (2 Days) after your last paid class of your term. If we have not received your payment 48 hours (2 Days) after your last paid class you will unfortunately forfeit your time slot.</p>
<p>You must notify your teacher 24 hours prior to your lesson if your circumstances change and you cannot attend your lesson. Cancellations under 24-hours will result in loss of fee. </p>


<p><strong>10 lesson package</strong> and get <strong>1 FREE</strong> (BEST VALUE)</p>
<ul>
<li><strong>Standard:</strong> $360 ($40 lessons)</li>
<li><strong>Peter Vox:</strong> $450 ($50 lessons)</li>
<li><strong>Peter Vox Premium:</strong> $585 ($65 lessons)</li>
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

<p>If you have any queries regarding your singing class including cancellations, rescheduling or lesson payments please contact your teacher directly on there mobile phone and for any other assistants please contact administration on <strong>0422 278 289</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>

<p>Thank you!</p>
<p><strong>Vox Singing Academy - Australia’s Voice Training Specialist - Est 1993</strong></p>
              "
            )
          );
        }

        // 72 hours (3 days) before the last class
        $first_last_index = count($event->schedules) - 1 >= 0 ? count($event->schedules) - 1 : 0;
        $first_last_schedule = $event->schedules[$first_last_index];
		
		if ($first_last_schedule->status == 6 )
			$status_valid = true;
			
        if(($first_last_schedule->start - 3*86400) > strtotime('0:00') &&
          (($first_last_schedule->start - 3*86400) < strtotime('0:00') + 86400) && $status_valid === true )
        {
          $student = $this->user_model->student_info($event->student_id);

          my_send_mail(
            array(
              'to' => $student->mail,
              'subject' => "COURTESY REMINDER | Vox Singing Academy",
              'message' => "
<p>Hi {$student->first_name},</p>
<p>We just wanted to let you know that your current term is due to be completed in 3 days.</p>
<p>To secure your time slot, please ensure your payment is received 48 hours (2 Days) after your last paid class. If we have not received your payment 48 hours (2 Days) after your last paid class you will unfortunately forfeit your time slot.</p>
<p>You must notify your teacher 24 hours prior to your lesson if your circumstances change and cannot attend your lesson. Cancellations under the 24-hour period will result in loss of fee.</p>


<p><strong>10 lesson package</strong> and get <strong>1 FREE</strong> (BEST VALUE)</p>
<ul>
<li><strong>Standard:</strong> $360 ($40 lessons)</li>
<li><strong>Peter Vox:</strong> $450 ($50 lessons)</li>
<li><strong>Peter Vox Premium:</strong> $585 ($65 lessons)</li>
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

<p>If you have any queries regarding your singing class including cancellations, rescheduling or lesson payments please contact your teacher directly on there mobile phone and for any other assistants please contact administration on <strong>0422 278 289</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>

<p>Thank you, </p>
<p><strong>Vox Singing Academy - Australia’s Voice Training Specialist - Est 1993</strong></p>
              "
            )
          );
        }

        // 24 hours (1 day) before the last class
		if ($first_last_schedule->status == 6 )
			$status_valid = true;
			
        // 24 hours (1 day) before the last class
        if(($first_last_schedule->start - 1*86400) > strtotime('0:00') &&
          (($first_last_schedule->start - 1*86400) < strtotime('0:00') + 86400) && $status_valid === true )
        {
          $student = $this->user_model->student_info($event->student_id);

          my_send_mail(
            array(
              'to' => $student->mail,
              'subject' => "COURTESY REMINDER | Vox Singing Academy",
              'message' => "
<p>Hi {$student->first_name},</p>
<p>We just want to let you know that your current term is due to be completed in 1 day.</p>
<p>To secure your time slot, please ensure your payment is received 48 hours (2 Days) after your last paid class. If we have not received your payment 48 hours (2 Days) after your last paid class you will unfortunately forfeit your time slot.</p>
<p>You must notify your teacher 24 hours prior to your lesson if your circumstances change and  you cannot attend your lesson. Cancellations under the 24-hour period will result in loss of fee.</p>


<p><strong>10 lesson package</strong> and get <strong>1 FREE</strong> (BEST VALUE)</p>
<ul>
<li><strong>Standard:</strong> $360 ($40 lessons)</li>
<li><strong>Peter Vox:</strong> $450 ($50 lessons)</li>
<li><strong>Peter Vox Premium:</strong> $585 ($65 lessons)</li>
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
<p>If you have any queries regarding your singing class including cancellations, rescheduling or lesson payments please contact your teacher directly on there mobile phone and for any other assistants please contact administration on <strong>0422 278 289</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
<p>Thank you, </p>
<p><strong>Vox Singing Academy - Australia’s Voice Training Specialist - Est 1993</strong></p>
              "
            )
          );
        }

		if ($first_last_schedule->status == 6 )
			$status_valid = true;
			
        // last emails
        if($first_last_schedule->start > strtotime('0:00') && $first_last_schedule->start < strtotime('0:00') + 86400 && $status_valid === true)
        {
          $student = $this->user_model->student_info($event->student_id);

          my_send_mail(
            array(
              'to' => $student->mail,
              'subject' => "COURTESY REMINDER | Vox Singing Academy",
              'message' => "
<p>Hi {$student->first_name},</p>

<p>We just want to let you know that your current term is completed and is due for renewal. To secure your time slot, please ensure your payment is received 48 hours (2 Days) after your last paid class. If we have not received your payment 48 hours (2 Days) after your last paid class you will unfortunately forfeit your time slot/singing lesson for next week.</p>


<p>You must notify your teacher 24 hours prior to your lesson if your circumstances change and cannot attend your lesson. </p>

<p>Cancellations under the 24-hour period will result in loss of fee.</p>

<p><strong>5 lesson package</strong></p>

<ul>
    <li><strong>$200</strong> ($40 lessons)</li>
    <li><strong>Peter Vox:</strong> $250 ($50 lessons)</li>
    <li><strong>Peter Vox Premium:</strong> $325 ($65 lessons)</li>
</ul>

<p><strong>9 lesson package</strong> and get <strong>1 FREE</strong> (BEST VALUE)</p>
<ul>
    <li><strong>$360</strong> ($40 lessons)</li>
    <li><strong>Peter Vox:</strong> $450 ($50 lessons)</li>
    <li><strong>Peter Vox Premium:</strong> $585 ($65 lessons)</li>
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

<p>If you have any queries regarding your singing class including cancellations, rescheduling or lesson payments please contact your teacher directly on there mobile phone and for any other assistants please contact administration on <strong>0422 278 289</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
<p>Thank you, </p>
<p><strong>Vox Singing Academy - Australia’s Voice Training Specialist - Est 1993</strong></p>
              "
            )
          );
        }
      } // end for($event_ids as $eid)

      // 24 hour auto-booking reminder response prior to any PAID and CONFIRMED scheduled event that they have lesson
      $schedule_limits = array(strtotime('+1 day midnight'), strtotime('+2 day midnight'), Schedule_model::STATUS_PAID_PENDING);
      $sql = "SELECT st.mail, st.first_name, u.first_name as teacher_first_name, stu.name, stu.street, stu.city, stu.postcode,
      s.sequence, s.start, s.end, u.mobile as teacher_mobile
      FROM schedules s
      INNER JOIN events e ON s.eid = e.eid
      INNER JOIN students st ON st.sid = e.student_id
      INNER JOIN users u ON u.uid = s.staff_id
      INNER JOIN studios stu ON s.studio_id = stu.sid
      WHERE s.start > ? AND s.start < ?
      AND s.status = ?";
      $result = $this->db->query($sql, $schedule_limits)->result();
      foreach($result as $row)
      {
        my_send_mail(
          array(
            'to' => $row->mail,
            'subject' => "REMINDER | Vox Singing Academy",
            'message' => "<p>Hi {$row->first_name},</p>
<p>You have a reservation with:</p>
<p><strong>Teacher:</strong> {$row->teacher_first_name}</p>
<p><strong>Mobile:</strong> {$row->teacher_mobile}</p>
<p><strong>Studio: </strong> {$row->street} {$row->city} {$row->postcode}</p>
<p><strong>Lesson {$row->sequence}</strong></p>
<p><strong>Start: " . date('d/m/Y g:ia', $row->start) . " - End: " . date('d/m/Y g:ia', $row->end) . "</strong></p>
<p><strong>Please note:</strong> All cancellations under 24 hours will result in loss of fee.</p>
<p>If you have any queries regarding your singing class including cancellations, rescheduling or lesson payments please contact your teacher directly on there mobile phone and for any other assistants please contact administration on <strong>0422 278 289</strong>. We look forward to seeing you soon and remember to have FUN singing!</p>
<p>Thank You!</p>
<p><strong>Vox Singing Academy - Australia’s Voice Training Specialist - Est 1993</strong></p>
          "
          )
        );
      }
    //} // end if($current_hour == $this->config->item('voxcrm_mail_time'))

  }

}