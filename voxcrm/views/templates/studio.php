<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/01/13
 * Time: 1:50 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<?php if(($errors = validation_errors())): ?>
<div class="alert alert-error">
  <a class="close" data-dismiss="alert">×</a>
  <?php echo $errors; ?>
</div>
<?php endif; ?>

<?php if($studio->sid): ?>
  <?php echo form_open('admin/studio/' . $studio->sid . '/save'); ?>
<?php else: ?>
  <?php echo form_open('admin/studio/0/save'); ?>
<?php endif; ?>

<?php echo form_label('Name', 'name'); ?>
<?php echo form_input('name', set_value('name', $studio->name), 'id="name" placeholder="name" required'); ?>
<?php echo form_label('Street', 'street'); ?>
<?php echo form_input('street', set_value('street', $studio->street), 'id="street" placeholder="street"'); ?>
<?php echo form_label('Additional street', 'additional'); ?>
<?php echo form_input('additional', set_value('additional', $studio->additional), 'id="additional" placeholder="street"'); ?>
<?php echo form_label('City', 'city'); ?>
<?php echo form_input('city', set_value('city', $studio->city), 'id="city" placeholder="city"'); ?>
<?php echo form_label('Postcode', 'postcode'); ?>
<?php echo form_input('postcode', set_value('postcode', $studio->postcode), 'id="postcode" placeholder="postcode" class="input-mini"'); ?>
<?php echo form_label('State', 'state'); ?>
<?php echo form_dropdown(
  'state',
  state_options(),
  ($this->input->post('state') ? $this->input->post('state') : $studio->state),
  'id="state" placeholder="state" class="input-small"'); ?>

<?php echo form_label('Phone', 'phone'); ?>
<?php echo form_input('phone', set_value('phone', $studio->phone), 'id="phone" placeholder="phone"'); ?>
<?php echo form_label('Fax', 'fax'); ?>
<?php echo form_input('fax', set_value('fax', $studio->fax), 'id="fax" placeholder="fax"'); ?>
<?php echo form_label('Description', 'description'); ?>
<?php echo form_textarea(array(
  'name' => 'description',
  'value' => set_value('description', $studio->description),
  'id' => 'description',
  'placeholder' => 'description',
  'rows' => 3)); ?>

<div class="form-actions">
  <?php echo form_submit('submit', 'Save', 'class="btn btn-primary"'); ?>
  <?php echo anchor('admin/products', 'Cancel', array('class' => 'btn')); ?>
</div>
<?php form_close(); ?>
