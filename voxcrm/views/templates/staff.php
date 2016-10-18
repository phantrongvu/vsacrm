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

<?php if($account->uid): ?>
  <?php echo form_open('people/staff/' . $account->uid . '/save'); ?>
<?php else: ?>
  <?php echo form_open('people/staff/0/save'); ?>
<?php endif; ?>

  <fieldset>
    <legend>Log-in info</legend>
    <?php echo form_label('Email', 'mail'); ?>

     <?php echo form_input('mail', set_value('mail', $account->mail), 'id="mail" placeholder="someone@example.com" required'); ?>

    <?php echo form_label('Password', 'pass'); ?>
    <?php echo form_password('pass', '', 'id="pass" placeholder="password"'); ?>
    <?php echo form_label('Confirm password', 'pass1'); ?>
    <?php echo form_password('pass1', '', 'id="pass1" placeholder="password"'); ?>

    <?php if($account->uid): ?>
      <span class="help-block">To change password, enter new password into fields above. To maintain old password, just leave them blank.</span>
    <?php endif; ?>

    <?php if( ! empty($roles)): ?>
      <?php echo form_label('Roles', 'roles'); ?>
      <?php foreach($roles as $role): ?>
        <label class="checkbox inline">
          <?php echo form_checkbox(
          array(
            'name' => 'roles[]',
            'id' => $role->name,
            'value' => $role->rid,
            'checked' => set_checkbox('roles', $role->rid, in_array($role->rid, array_keys($account->roles)) ? TRUE : FALSE),
          )); ?>
          <?php echo $role->name; ?>
        </label>
      <?php endforeach; ?>
    <?php endif; ?>
  </fieldset>

  <div class="row">
    <div class="columns col-xs-6">
      <fieldset>
        <legend>General info</legend>
        <?php echo form_label('First name', 'first_name'); ?>
        <?php echo form_input('first_name', set_value('first_name', $account->first_name), 'id="first_name" placeholder="First name" required'); ?>
        <?php echo form_label('Last name', 'last_name'); ?>
        <?php echo form_input('last_name', set_value('last_name', $account->last_name), 'id="last_name" placeholder="Last name" required'); ?>
        <?php echo form_label('Rate', 'rate'); ?>
        <?php echo form_input('rate', set_value('rate', $account->rate), 'id="rate" placeholder="Rate" required'); ?>
        <?php echo form_label('Availability (working hours/week)', 'availability'); ?>
        <?php echo form_input('availability', set_value('availability', $account->availability), 'id="availability" placeholder="Availability" required'); ?>
        <?php echo form_label('Date of Birth (dd/mm/yyyy)', 'dob'); ?>
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
    <?php echo anchor('people/search/staff', 'Cancel', array('class' => 'btn')); ?>
  </div>
<?php echo form_close(); ?>
