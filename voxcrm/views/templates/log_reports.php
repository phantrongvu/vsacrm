<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style type="text/css">
.table th, .table td {
	text-align: center;
}
.table tr td:first-child {
	text-align: left;
	font-weight: 700;
}
</style>
<?php
$check=array();
for($i=0;$i<count($result);$i++){
	$day = strtolower(date('D', strtotime($result[$i]->log_date)));		
	$check[$day]=	array(
							"student_data"					=>	$result[$i]->student_data,
							"bpoints"						=>	$result[$i]->bpoints,
							"pays"							=>	$result[$i]->pays, 
							"report"						=>	$result[$i]->report,
							"crm_updated"					=>	$result[$i]->crm_updated,
							"phones"						=>	$result[$i]->phones,
							"emails"						=>	$result[$i]->emails,
							"return_calls"					=>	$result[$i]->return_calls, 
							"banking"						=>	$result[$i]->banking,
							"check_back_end_emails"			=>  $result[$i]->check_back_end_emails,
							"test_website_inquiry_forms"	=>  $result[$i]->test_website_inquiry_forms
							);
}

  $attributes = array('class' => 'reportslog', 'id' => 'reportform');
  echo form_open('report/log_reports', $attributes);
  
		 $mydate = getdate(date('U'));
		 $today = "$mydate[weekday]";
		 $enable_checkbox = '';
		 $disable_checkbox = 'disabled="disabled"';
	   ?>
<br/>
<h3><?php echo 'Week Number: '.$weekNumber = date("W"); ?></h3>
<table class="table table-bordered table-hover">
  <thead class="success">
    <tr class="text-success">
      <th><?php echo date('F j, Y'); ?></th>
      <th>Monday</th>
      <th>Tuesday</th>
      <th>Wednesday</th>
      <th>Thursday</th>
      <th>Friday</th>
      <th>Saturday</th>
      <th>Sunday</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Student Data</td>
      <td><input type="checkbox" name="student_data" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'monday', $ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'wednesday', $ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="thursday"  <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="student_data" value="sunday"  <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["student_data"]!=0){$ck="TRUE";} echo set_checkbox('student_data', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Bpoints</td>
      <td><input type="checkbox" name="bpoints" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'monday', $ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'wednesday', $ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="thursday"  <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="bpoints" value="sunday"  <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('bpoints', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Pays</td>
      <td><input type="checkbox" name="pays" value="monday"  <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["pays"]!=0){$ck="TRUE";} echo set_checkbox('pays', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["pays"]!=0){$ck="TRUE";} echo set_checkbox('pays', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["bpoints"]!=0){$ck="TRUE";} echo set_checkbox('pays', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["pays"]!=0){$ck="TRUE";} echo set_checkbox('pays', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["pays"]!=0){$ck="TRUE";} echo set_checkbox('pays', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["pays"]==1){
			$ck="TRUE";
		} echo set_checkbox('pays', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="pays" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["pays"]!=0){$ck="TRUE";}  echo set_checkbox('pays', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Report</td>
      <td><input type="checkbox"  name="report" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["report"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('report', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox"  name="report" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["report"]!=0){$ck="TRUE";} echo set_checkbox('report', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>CRM updated</td>
      <td><input type="checkbox" name="crm_updated" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["crm_updated"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('crm_updated', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="crm_updated" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["crm_updated"]!=0){$ck="TRUE";} echo set_checkbox('crm_updated', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Phones</td>
      <td><input type="checkbox" name="phones" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["phones"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('phones', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="phones" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["phones"]!=0){$ck="TRUE";} echo set_checkbox('phones', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Emails</td>
      <td><input type="checkbox" name="emails" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["emails"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('emails', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="emails" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["emails"]!=0){$ck="TRUE";} echo set_checkbox('emails', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Return Calls</td>
      <td><input type="checkbox" name="return_calls" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["return_calls"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('return_calls', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="return_calls" value="sunday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["return_calls"]!=0){$ck="TRUE";} echo set_checkbox('return_calls', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>Banking</td>
      <td><input type="checkbox" name="banking" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["banking"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('banking', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="banking" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["banking"]!=0){$ck="TRUE";} echo set_checkbox('banking', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
<tr>      <td>Check Back-end Emails</td>
      <td><input type="checkbox" name="check_back_end_emails" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["check_back_end_emails"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('check_back_end_emails', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="check_back_end_emails" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["check_back_end_emails"]!=0){$ck="TRUE";} echo set_checkbox('check_back_end_emails', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
<tr>      <td>Test Website Inquiry Forms</td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="monday" <?php $ck=""; if(isset($check["mon"]) and $check["mon"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'monday',$ck); ?> <?php if($today =='Monday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="tuesday" <?php $ck=""; if(isset($check["tue"]) and $check["tue"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'tuesday',$ck); ?> <?php if($today =='Tuesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="wednesday" <?php $ck=""; if(isset($check["wed"]) and $check["wed"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'wednesday',$ck); ?> <?php if($today =='Wednesday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="thursday" <?php $ck=""; if(isset($check["thu"]) and $check["thu"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'thursday',$ck); ?> <?php if($today =='Thursday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="friday" <?php $ck=""; if(isset($check["fri"]) and $check["fri"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'friday',$ck); ?> <?php if($today =='Friday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="saturday" <?php $ck=""; if(isset($check["sat"]) and $check["sat"]["test_website_inquiry_forms"]=="1"){
			$ck="TRUE";
		} echo set_checkbox('test_website_inquiry_forms', 'saturday',$ck); ?> <?php if($today =='Saturday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
      <td><input type="checkbox" name="test_website_inquiry_forms" value="sunday" <?php $ck=""; if(isset($check["sun"]) and $check["sun"]["test_website_inquiry_forms"]!=0){$ck="TRUE";} echo set_checkbox('test_website_inquiry_forms', 'sunday',$ck); ?> <?php if($today =='Sunday') { echo $enable_checkbox; }else{ echo $disable_checkbox;}  ?> /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
<?php
$data = array(
    'name'        => 'save_report',
    'id'          => 'save-report',
	'class'       => 'btn btn-default',
    'value'       => 'Save Reports',
    );

echo form_submit($data);

?>
<!--<button class="btn btn-default">Print PDF</button>--> 
<?php echo form_close(); ?> 