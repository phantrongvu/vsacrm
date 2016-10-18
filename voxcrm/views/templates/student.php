<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 29/12/12
 * Time: 10:56 AM
 * To change this template use File | Settings | File Templates.
 */
?>

<?php if(($errors = validation_errors())): ?>
<div class="alert alert-error">
  <a class="close" data-dismiss="alert">×</a>
  <?php echo $errors; ?>
</div>
<?php endif; ?>

<?php if($account->sid): ?>
  <?php echo form_open('people/student/' . $account->sid . '/save'); ?>
<?php else: ?>
  <?php echo form_open('people/student/0/save'); ?>
<?php endif; ?>

<fieldset>
  <legend>Register info </legend>
  <?php echo form_label('Email', 'mail'); ?>

    <?php echo form_input('mail', set_value('mail', $account->mail), 'id="mail" placeholder="someone@example.com" required'); ?>


</fieldset>

<div class="row">
  <div class="columns col-xs-6">
    <fieldset>
      <legend>General info</legend>
      <?php echo form_label('First name', 'first_name'); ?>
      <?php echo form_input('first_name', set_value('first_name', $account->first_name), 'id="first_name" placeholder="First name" required'); ?>
      <?php echo form_label('Last name', 'last_name'); ?>
      <?php echo form_input('last_name', set_value('last_name', $account->last_name), 'id="last_name" placeholder="Last name" required'); ?>
      <?php echo form_label('DOB', 'dob'); ?>
      <?php echo form_input('dob', set_value('dob', $account->dob), 'id="dob" placeholder="dd/mm/yyyy"'); ?>
      <?php echo form_label('Phone', 'phone'); ?>
      <?php echo form_input('phone', set_value('phone', $account->phone), 'id="phone" placeholder="phone"'); ?>
      <?php echo form_label('Mobile', 'mobile'); ?>
      <?php echo form_input('mobile', set_value('mobile', $account->mobile), 'id="mobile" placeholder="mobile"'); ?>
      <?php echo form_label('Description', 'description'); ?>
      <?php echo form_textarea(array(
      'name' => 'description',
      'value' => set_value('description', $account->description),
      'id' => 'description',
      'placeholder' => 'description',
      'rows' => 2)); ?>
    </fieldset>
  </div>
  <div class="columns col-xs-6">
    <fieldset>
      <legend>Address</legend>
      <?php echo form_label('Street', 'street'); ?>
      <?php echo form_input('street', set_value('street', $account->street), 'id="street" placeholder="street"'); ?>
      <?php echo form_label('Additional street', 'additional'); ?>
      <?php echo form_input('additional', set_value('additional', $account->additional), 'id="additional" placeholder="street"'); ?>
      <?php echo form_label('City', 'city'); ?>
      <?php echo form_input('city', set_value('city', $account->city), 'id="city" placeholder="city"'); ?>
      <?php echo form_label('Postcode', 'postcode'); ?>
      <?php echo form_input('postcode', set_value('postcode', $account->postcode), 'id="postcode" placeholder="postcode"'); ?>
      <?php echo form_label('State', 'state'); ?>
      <?php echo form_dropdown(
      'state',
      state_options(),
      ($this->input->post('state') ? $this->input->post('state') : $account->state),
      'id="state" placeholder="state"'); ?>
    </fieldset>
  </div>
</div>

<div class="form-actions">
  <?php echo form_submit('submit', 'Save', 'class="btn btn-primary"'); ?>
  <?php echo anchor('people/search/student', 'Cancel', array('class' => 'btn')); ?>

  <?php if($account->sid): ?>
    <br />
    <br />
  <?php echo anchor('people/student/' . $account->sid, 'Delete',
    array(
      'class' => 'btn btn-small confirm-delete btn-danger',
      'data-type' => 'student',
      'data-id' => $account->sid,
    )) ?>

    <?php echo anchor('calendar/event?sid=' . $account->sid, 'Add event', array('class' => 'btn btn-small')) ?>
    <?php echo anchor('note/search/' . $account->sid, 'Note history', array('class' => 'btn btn-small')) ?>
    <?php echo anchor('note/manage?sid=' . $account->sid . '&destination=people/student/' . $account->sid, 'Add Notes', array('class' => 'btn btn-small')) ?>

    <div id="modal-delete-person" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="model-delete-label">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="model-delete-label">Delete a person</h3>
      </div>
      <div class="modal-body">
        <p>You are about to delete one person, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
      </div>
      <div class="modal-footer">
        <?php echo anchor('people/delete/student/1', 'Yes', array('class' => 'btn btn-danger')); ?>
        <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
      </div>
    </div>
  <?php endif; ?>
<?php echo form_close(); ?>
