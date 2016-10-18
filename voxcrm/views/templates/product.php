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

<?php if($product->pid): ?>
  <?php echo form_open('admin/product/' . $product->pid . '/save'); ?>
<?php else: ?>
  <?php echo form_open('admin/product/0/save'); ?>
<?php endif; ?>

<?php echo form_label('Name', 'name'); ?>
<?php echo form_input('name', set_value('name', $product->name), 'id="name" placeholder="name" required'); ?>
<?php echo form_label('Number of lessons', 'lessons'); ?>
<?php if($product->in_active_event): ?>
  <span class="uneditable-input input-small"><?php echo $product->lessons; ?></span>
  <?php echo form_hidden('lessons', $product->lessons); ?>
<span class="help-block">This product is currently in active event, therefore number of lessons cannot be changed.</span>
<?php else: ?>
  <?php echo form_input('lessons', set_value('lessons', $product->lessons), 'id="lessons" placeholder="# lessons" class="input-small" required'); ?>
<?php endif; ?>
<?php echo form_label('Recurring', 'recurring'); ?>
<?php echo form_dropdown(
  'recurring',
  product_recurring(),
  ($this->input->post('recurring') ? $this->input->post('recurring') : $product->recurring),
  'id="recurring" placeholder="recurring" class="input-small"'); ?>
<?php echo form_label('Price', 'price'); ?>
<div class="input-prepend">
  <span class="add-on">AUD</span>
  <?php echo form_input('price', set_value('price', $product->price), 'id="price" placeholder="price" class="label-prepend input-small" required'); ?>
</div>
<?php echo form_label('Description', 'description'); ?>
<?php echo form_textarea(array(
  'name' => 'description',
  'value' => set_value('description', $product->description),
  'id' => 'description',
  'placeholder' => 'description',
  'rows' => 3)); ?>

<div class="form-actions">
  <?php echo form_submit('submit', 'Save', 'class="btn btn-primary"'); ?>
  <?php echo anchor('admin/products', 'Cancel', array('class' => 'btn')); ?>
</div>
<?php form_close(); ?>
