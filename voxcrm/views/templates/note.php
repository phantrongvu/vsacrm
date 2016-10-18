<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 20/01/13
 * Time: 11:30 AM
 * To change this template use File | Settings | File Templates.
 */
?>
<?php $note = $_note; ?>
<?php if(($errors = validation_errors())): ?>
<div class="alert alert-error">
  <a class="close" data-dismiss="alert">×</a>
  <?php echo $errors; ?>
</div>
<?php endif; ?>

<?php if($note->nid): ?>
  <?php echo form_open('note/manage/' . $note->nid . '/save'); ?>
<?php else: ?>
  <?php echo form_open('note/manage/0/save'); ?>
<?php endif; ?>

<?php echo form_hidden('destination', set_value('destination', isset($destination) ? $destination : '')); ?>
<?php echo form_hidden('view', set_value('view', isset($calendar_view) ? $calendar_view['view'] : '')); ?>
<?php echo form_hidden('start', set_value('start', isset($calendar_view) ? $calendar_view['start'] : '')); ?>
<?php echo form_label('Student', 'student_search'); ?>
<?php
echo form_hidden('student',
  set_value(
    'student',
    isset($default_student) ? $default_student->sid :
      ( isset($note->sid) ? $note->sid : '' )
  )
);
?>

<?php
  echo form_input('student_search',
    set_value('student_search',
      isset($default_student) ? $this->user_model->display_name($default_student) :
        ( isset($note->student) ? $this->user_model->display_name($note->student) : '' )
    ),
    'id="student_search" placeholder="Enter first name or last name or email to search" class="student-autocomplete input-xxlarge" required');
?>

<?php echo form_label('Activity Title', 'title'); ?>
<?php
$options = array(
  'Songs/Scales' => 'Songs/Scales',
  'Songs' => 'Songs',
  'Scales' => 'Scales',
  'Breathing/Ball' => 'Breathing/Ball',
  'VBC' => 'VBC',
  'Pitching Scales/Ear Training' => 'Pitching Scales/Ear Training',
  'Future or Pending Scales' => 'Future or Pending Scales',
  'Test/Revision' => 'Test/Revision',
  'Cold Management/Vocal Health' => 'Cold Management/Vocal Health',
  'Mic Tech/Stage Pres/Crowd Interaction' => 'Mic Tech/Stage Pres/Crowd Interaction',
  'Songwriting/Industry Advice' => 'Songwriting/Industry Advice',
  '1st Lesson' => '1st Lesson',
  'Teachers Notes' => 'Teachers Notes',
  'Admin Notes' => 'Admin Notes',
);
echo form_dropdown('title', $options, $note->title);
?>
<?php echo form_label('Activity Notes', 'body'); ?>
<?php echo form_textarea(array(
  'name' => 'body',
  'value' => set_value('body', $note->body),
  'id' => 'body',
  'placeholder' => 'Activity Notes',
  'rows' => 3,
  'required' => TRUE)); ?>

<div class="form-actions">
  <?php echo form_submit('submit', 'Save', 'class="btn btn-primary"'); ?>
  <?php echo anchor('note/search/' . (isset($default_student) ? $default_student->sid :
  ( isset($note->sid) ? $note->sid : '' )), 'Cancel', array('class' => 'btn')); ?>
</div>
<?php form_close(); ?>

