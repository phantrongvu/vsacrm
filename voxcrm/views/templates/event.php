<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 8/01/13
 * Time: 10:56 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<?php if(($errors = validation_errors())): ?>
<div class="alert alert-error">
  <a class="close" data-dismiss="alert">×</a>
  <?php echo $errors; ?>
</div>
<?php endif; ?>

<?php if($event->eid && $event->schedule->cid): ?>
<?php echo form_open('calendar/event/' . $event->eid . '_' . $event->schedule->cid . '/save'); ?>
<?php else: ?>
<?php echo form_open('calendar/event/0/save'); ?>
<?php endif; ?>

<?php echo form_label('Name', 'name'); ?>
<?php
  echo form_input('name',
    set_value(
      'name',
      isset($default_student) ? $default_student->first_name . ' ' . $default_student->last_name : $event->name
    ),
    'id="name" placeholder="name" required');
?>

<fieldset style="margin-bottom: 20px;">
  <legend>When</legend>
  <div class="row date_fields">
	<div class="date_field">
		<?php echo form_label('Date', 'when'); ?>
		<?php if( ! empty($event->eid)): ?>
		  <span class="uneditable-input input-small"><?php echo date('d/m/Y', $event->schedule->start); ?></span>
		<?php else: ?>
		  <?php
			echo form_input('scheduled_date',
			  set_value(
				'scheduled_date',
				isset($event->schedule) ? date('d/m/Y', $event->schedule->start) : date('d/m/Y')
			  ),
			'id="scheduled_date" class="input-small" required
			pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"');
		  ?>
		<?php endif; ?>
	</div>
	<div class="from_to">
		<div class="date_field">
			<?php echo form_label('From', 'scheduled_time_from'); ?>
			<?php if( ! empty($event->eid)): ?>
			  <span class="uneditable-input input-mini"><?php echo date('G:i', $event->schedule->start); ?></span>
			<?php else: ?>
			  <?php echo form_dropdown(
				'scheduled_time_from',
				time_options(),
				$this->input->post('scheduled_time_from') ? $this->input->post('scheduled_time_from') :
				  ( isset($event->schedule) ? time_option($event->schedule->start) : '' ),
				'id="scheduled_time_from" class="dropdown input-small" required'); ?>
			<?php endif; ?>
		</div>
		<div class="date_field">
			<?php echo form_label('To', 'scheduled_time_to'); ?>
			<?php if( ! empty($event->eid)): ?>
			  <span class="uneditable-input input-mini"><?php echo date('G:i', $event->schedule->end); ?></span>
			<?php else: ?>
			  <?php echo form_dropdown(
				'scheduled_time_to',
				time_options('to'),
				$this->input->post('scheduled_time_to') ? $this->input->post('scheduled_time_to') :
				  (  isset($event->schedule) ? time_option($event->schedule->end) : '' ),
				'id="scheduled_time_to" class="dropdown input-small" required'); ?>
			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	  </div>
  </div>
</fieldset>

<?php echo form_label('Student', 'student_search'); ?>
<?php
  echo form_hidden('student',
    set_value(
      'student',
      isset($default_student) ? $default_student->sid :
        ( isset($event->student) ? $event->student_id : '' )
    )
  );
?>

<?php if( ! empty($event->eid)): ?>
  <span class="uneditable-input input-xxlarge"><?php echo $this->user_model->display_name($event->student); ?></span>
<?php else: ?>
  <?php
    echo form_input('student_search',
      set_value('student_search',
        isset($default_student) ? $this->user_model->display_name($default_student) :
          ( isset($event->student) ? $this->user_model->display_name($event->student) : '' )

      ),
    'id="student_search" placeholder="Enter first name or last name or email to search" class="student-autocomplete input-xxlarge" required');
  ?>
<?php endif; ?>
<?php echo form_label('Teacher', 'teacher'); ?>
<?php echo form_dropdown(
  'teacher',
  $teacher_options,
  $this->input->post('teacher') ? $this->input->post('teacher') :
    ( isset($event->schedule) ? $event->schedule->staff_id : '' ),
  'id="teacher" class="input-xxlarge" required'); ?>

<?php if( ! empty($event->eid)): ?>
<?php echo form_label('Product', 'product required'); ?>
  <span class="uneditable-input"><?php echo $product_options[$event->product_id]; ?></span>
<?php else: ?>
  <?php echo form_label('Product', 'product required'); ?>
  <?php echo form_dropdown(
    'product',
    $product_options,
    ($this->input->post('product') ? $this->input->post('product') : $event->product_id),
    'id="product" required'); ?>
<?php endif; ?>

<?php echo form_label('Studio', 'studio'); ?>
<?php echo form_dropdown(
  'studio',
  $studio_options,
  $this->input->post('studio') ? $this->input->post('studio') :
    ( isset($event->schedule) ? $event->schedule->studio_id : '' ),
  'id="studio" required'); ?>

<?php echo form_label('Description', 'description'); ?>
<?php echo form_textarea(array(
  'name' => 'description',
  'value' => set_value('description', $event->description),
  'id' => 'description',
  'placeholder' => 'description',
  'rows' => 3)); ?>

<?php if( ! empty($event->eid)): ?>
  <label class="checkbox">
    <?php echo form_checkbox('all_lessons', 'all'); ?> Also apply for other lessons of this event?
  </label>
<?php endif; ?>

<label class="checkbox">
  <input type="checkbox" name="email_notification" />  Email notification
</label>

<div class="form-actions">
  <?php echo form_submit('submit', 'Save', 'class="btn btn-primary"'); ?>
</div>

<?php echo form_close(); ?>
