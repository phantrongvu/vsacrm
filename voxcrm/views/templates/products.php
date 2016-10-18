<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/01/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<p>&nbsp;</p>
<?php echo anchor('admin/product', 'Add product', array('class' => 'btn btn-primary')); ?>
<p>&nbsp;</p>

<?php if( ! empty($products) && count($products)): ?>
<table class="table table-striped table-hover table-bordered responsive">
  <thead>
  <tr>
    <th></th>
    <th>Name</th>
    <th data-hide="xsmall,phone">Number of lessons</th>
    <th data-hide="xsmall,phone">Price</th>
    <th data-hide="xsmall">Actions</th>
  </tr>
  </thead>

  <tbody>
    <?php foreach($products as $product): ?>
      <tr>
        <td>&nbsp;</td>
        <td><?php echo $product->name; ?></td>
        <td><?php echo $product->lessons; ?></td>
        <td>AUD <?php echo number_format($product->price, 2); ?></td>
        <td>
          <?php echo anchor('admin/product/' . $product->pid, 'Edit', array('class' => 'btn btn-small')) ?>
          <?php echo anchor('admin/product/' . $product->pid, 'Delete',
          array(
            'class' => 'btn btn-small confirm-delete btn-danger',
            'data-id' => $product->pid,
          )) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="modal-delete-product" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="model-delete-label">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="model-delete-label">Delete a product</h3>
  </div>
  <div class="modal-body">
    <p>You are about to delete a product. Delete a product will also erase any data associated with it.</p>
    <p>This procedure is irreversible.</p>
    <p>Do you want to proceed?</p>
  </div>
  <div class="modal-footer">
    <?php echo anchor('admin/products', 'Yes', array('class' => 'btn btn-danger')); ?>
    <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
  </div>
</div>

<?php else: ?>
  <p>There's currently no product. Please click <strong>Add product</strong> button above to continue.</p>
<?php endif; ?>
