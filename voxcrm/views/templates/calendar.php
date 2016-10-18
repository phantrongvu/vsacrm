<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
	/**
		* Created by JetBrains PhpStorm.
		* User: phantrongvu
		* Date: 12/01/13
		* Time: 10:41 PM
		* To change this template use File | Settings | File Templates.
	*/
?>

<?php echo form_open('calendar', 'class="form-inline"'); ?>
<fieldset>
    <legend><h5>Filter by</h5></legend>
	<div class="filter_date">
		<div class="fields">
			<?php echo form_label('Studio', 'studio_id'); ?>
			<?php echo form_dropdown(
				'studio_id',
				(array('' => 'Please select') + $this->studio_model->list_options()),
				($this->session->userdata('studio_id_filter') ? $this->session->userdata('studio_id_filter') : ''),
			'id="studio_id"'); ?>
		</div>
	</div>
    <?php if($view == 'admin'): ?>
		<div class="filter_date">
			<div class="fields">
				<?php echo form_label('Teacher', 'teacher_id'); ?>
				<?php echo form_dropdown(
					'teacher_id',
					(array('' => 'Please select') + $this->user_model->list_options()),
					($this->session->userdata('teacher_id_filter') ? $this->session->userdata('teacher_id_filter') : ''),
				'id="teacher_id"'); ?>
			</div>
		</div>
    <?php endif; ?>
	<div class="filter_date_btn">
		<div class="fields">
			<?php echo form_submit('submit', 'Go', 'class="btn btn-primary"'); ?>
			<?php echo anchor('calendar/reset_filter', 'Reset', array('class' => 'btn')); ?>
			<?php if($view == 'teacher'): ?>
				<?php echo anchor('#', 'Email admin', array('class' => 'btn btn-email-admin')); ?>
			<?php endif; ?>
		</div>
	</div>
    
</fieldset>
<?php echo form_close(); ?>

<hr />
<div style="margin-bottom: 16px;">
	<small>
		<h6 style="margin: 0 0 4px;">Legend</h6>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_DEFAULT; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_DEFAULT ?>;">Default</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_ONE_LEFT; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_ONE_LEFT ?>;">One lesson left</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_TWO_LEFT; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_TWO_LEFT ?>;">Two lessons left</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_FIRST_LESSON; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_FIRST_LESSON ?>;">First lesson</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_FIRST_LESSON_UNPAID; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_FIRST_LESSON_UNPAID ?>;">Unpaid</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_CANCELED_OVER_24; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_CANCELED_OVER_24 ?>;">Lesson cancelled over 24 hours</span>
		<span class="schedule_legend" style="background-color: <?php echo Calendar::BACK_CANCELED_UNDER_24; ?>;
		padding: 5px;
		color: <?php echo Calendar::TEXT_CANCELED_UNDER_24 ?>;">Lesson cancelled under 24 hours</span>
	</small>
	<div class="clearfix"></div>
</div>
<div id="calendar"></div>

<small>
	<div class="modal hide fade event-action-modal">
		<?php echo form_open('calendar/schedule/status', array('class' => 'event-action-form')); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Schedule details</h3>
		</div>
		
		<div class="modal-body">
			
			
			<table class="table table-condensed table-bordered">
				<tr class="info">
					<td width="50%">
						<span class="extra-actions"></span><br />
						<span class="student-name" style="font-weight: bold;"></span><br />
						<strong>E:</strong> <span class="student-mail"></span><br />
						<strong>P:</strong> <span class="student-phone"></span><br />
						<strong>M:</strong> <span class="student-mobile"></span>
					</td>
					<td width="50%">
						<div class="note-history"></div>
					</td>
				</tr>
			</table>
			
			<hr />
			<!-- radios -->
			<div class="hide">
				<input type="radio" name="status" value="<?php echo Schedule_model::STATUS_CANCELED_OVER_24 ?>" checked class="rad-over-24">
				<input type="radio" name="status" value="<?php echo Schedule_model::STATUS_CANCELED_IN_24 ?>" class="rad-within-24">
				<input type="radio" name="status" value="<?php echo Schedule_model::STATUS_DELETED ?>" class="rad-delete-event">
				<input type="radio" name="status" value="rebook" class="rad-rebook">
			</div>
			<!-- end radios -->
			
			<!-- accordion -->
			<div id="accordion">
				<!-- Cancel under 24 hours -->
				<h3>Cancel under 24 hours</h3>
				<div>
					<div class="cancel-schedule-within-24">
						<div class="form-inline">
							<?php echo form_label('Reason', 'description_1'); ?>
							<?php echo form_dropdown(
								'description_1',
								$this->reason_model->reason_options(),
								'',
							'id="description_1"'); ?>
						</div>
						
						<label class="checkbox">
							<input type="checkbox" name="email_notification_2" />  Email notification
						</label>
					</div>
				</div>
				
				<!-- cancel over 24 hours -->
				<h3>Cancel over 24 hours</h3>
				<div>
					<div class="cancel-schedule-over-24">
						<div class="form-inline">
							<?php echo form_label('Date&nbsp;', 'when'); ?>
							<?php echo form_input('scheduled_date', set_value('scheduled_date', date('d/m/Y')),
								'id="scheduled_date" class="input-small"
							pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"'); ?>
							<?php echo form_label('&nbsp;From&nbsp;', 'scheduled_time_from'); ?>
							<?php echo form_dropdown(
								'scheduled_time_from',
								time_options(),
								($this->input->post('scheduled_time_from') ? $this->input->post('scheduled_time_from') : ''),
							'id="scheduled_time_from" class="input-small"'); ?>
							<?php echo form_label('&nbsp;To&nbsp;', 'scheduled_time_to'); ?>
							<?php echo form_dropdown(
								'scheduled_time_to',
								time_options('to'),
								($this->input->post('scheduled_time_to') ? $this->input->post('scheduled_time_to') : ''),
							'id="scheduled_time_to" class="input-small"'); ?>
						</div>
						
						<div class="form-inline">
							<?php echo form_label('Reason', 'description'); ?>
							<?php echo form_dropdown(
								'description',
								$this->reason_model->reason_options(),
								'',
							'id="description"'); ?>
						</div>
						
						<label class="checkbox">
							<input type="checkbox" name="email_notification_1" />  Email notification
						</label>
					</div>
				</div>
				
				<?php if($view == 'admin'): ?>
				<!-- Delete event -->
				<h3>Delete whole event</h3>
				<div>
					<div class="cancel-schedule-delete-event">
						<span class="text-error"><em>Caution: Applying this will remove every lessons of the event!</em></span>
						<?php echo form_label('Reason', 'description_2'); ?>
						<?php echo form_dropdown(
							'description_2',
							$this->delete_reason_model->reason_options(),
							'',
						'id="description_2"'); ?>
						
						<label class="checkbox">
							<input type="checkbox" name="email_notification_3" />  Email notification
						</label>
					</div>
				</div>
				<?php endif; ?>
				
				<!-- Rebook -->
				<h3 class="title-rebook">Rebook</h3>
				<div class="panel-rebook">
					<div class="rebook-event">
						<div class="form-inline" style="margin-bottom: 12px;">
							<?php echo form_label('Date&nbsp;', 'when'); ?>
							<?php echo form_input('rebook_date', set_value('rebook_date', date('d/m/Y')),
								'id="rebook_date" class="input-small"
							pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"'); ?>
							<?php echo form_label('&nbsp;From&nbsp;', 'scheduled_time_from'); ?>
							<?php echo form_dropdown(
								'rebook_time_from',
								time_options(),
								($this->input->post('rebook_time_from') ? $this->input->post('rebook_time_from') : ''),
							'id="rebook_time_from" class="input-small"'); ?>
							<?php echo form_label('&nbsp;To&nbsp;', 'scheduled_time_to'); ?>
							<?php echo form_dropdown(
								'rebook_time_to',
								time_options('to'),
								($this->input->post('rebook_time_to') ? $this->input->post('rebook_time_to') : ''),
							'id="rebook_time_to" class="input-small"'); ?>
							<br />
							<?php echo form_label('Product', 'product'); ?>
							<?php echo form_dropdown(
								'product',
								$product_options,
								//$single_lesson,
								($this->input->post('product') ? $this->input->post('product') : ''),
							'id="product"'); ?>
						</div>
						
						<label class="checkbox" style="float: left;">
							<input type="checkbox" name="email_notification_4" />  Email notification
						</label>
					</div>
				</div>
			</div>
			<!-- end accordion -->
			
		</div>
		<div class="modal-footer">
			<?php echo form_submit('submit', 'Submit', 'class="btn btn-primary"'); ?>
			<a href="#" class="btn" data-dismiss="modal">Close</a>
		</div>
		<input type="hidden" name="current_calendar_view" class="current-calendar-view" value="" />
		<input type="hidden" name="current_calendar_start" class="current-calendar-start" value="" />
		<?php echo form_close(); ?>
	</div>
</small>

<div id="dialog-form" title="Re-schedule lesson" class="hide">
	<small class="text-info">You are re-scheduling a lesson. Do you want to apply following actions?</small>
	<p></p>
	<fieldset>
		<label class="checkbox">
			<input type="checkbox" class="apply-whole-event"> Apply for the whole event.
		</label>
		<label class="checkbox">
			<input type="checkbox" class="send-email-notification"> Email notification.
		</label>
	</fieldset>
</div>

<?php if($view == 'teacher'): ?>
<small>
	<div title="Email admin" class="hide modal fade dialog-email-admin">
		<?php echo form_open('admin/technical_email', array('class' => 'form-horizontal')); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Email admin</h3>
		</div>
		<div class="modal-body">
			<fieldset>
				<div class="control-group">
					<label class="control-label">Name</label>
					<div class="controls">
						<p class="uneditable-input"><?php print $user->first_name ?> <?php print $user->last_name ?></p>
						<input type="hidden" name="email_uid" value="<?php print $user->uid ?>" />
						<input type="hidden" name="email_teacher" value="<?php print $user->first_name ?> <?php print $user->last_name ?>" />
						<input type="hidden" name="email_start" class="email-current-start" value="<?php print time(); ?>" />
						<input type="hidden" name="email_view" class="email-current-view" value="agendaWeek" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email_studio">Studio</label>
					<div class="controls">
						<?php echo form_dropdown(
							'email_studio',
							($this->studio_model->list_options()),
							($this->input->post('email_studio') ? $this->input->post('email_studio') : ''),
						'id="email_studio" required="required"'); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email_reason">Reason</label>
					<div class="controls">
						<?php echo form_dropdown(
							'email_reason',
							$this->email_admin_reason->reason_options(),
							'',
						'id="email_reason" required="required"'); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email_message">Message</label>
					<div class="controls">
						<textarea rows="3" id="email_message" class="input-xlarge" required="required" name="email_message"></textarea>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="modal-footer">
			<?php echo form_submit('submit', 'Submit', 'class="btn btn-primary"'); ?>
			<a href="#" class="btn" data-dismiss="modal">Close</a>
		</div>
		<?php echo form_close(); ?>
	</div>
</small>
<?php endif; ?>