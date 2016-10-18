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
<?php echo anchor('admin/studio', 'Add studio', array('class' => 'btn btn-primary')); ?>
<p>&nbsp;</p>

<?php if( ! empty($studios) && count($studios)): ?>
  <table class="table table-striped table-hover table-bordered responsive">
    <thead>
    <tr>
      <th></th>
      <th>Name</th>
      <th data-hide="xsmall,phone,small">Address</th>
      <th>Phone</th>
      <th>Actions</th>
    </tr>
    </thead>
  <tbody>
  <?php foreach($studios as $studio): ?>
    <tr>
      <td>&nbsp;</td>
      <td><?php echo $studio->name; ?></td>
      <td><address>
        <?php
        echo implode('<br />',
          array(
            $studio->street . ($studio->additional ? '<br />' . $studio->additional : ''),
            $studio->city,
            $studio->postcode,
            $studio->state,
          ));
        ?>
      </address></td>
      <td>
        <address>
          <abbr title="Phone">P:</abbr> <?php echo $studio->phone; ?><br />
          <abbr title="Fax">F:</abbr> <?php echo $studio->fax; ?>
        </address>
      </td>
      <td>
        <?php echo anchor('admin/studio/' . $studio->sid, 'Edit', array('class' => 'btn btn-small')) ?>
        <?php echo anchor('admin/studio/' . $studio->sid, 'Delete',
        array(
          'class' => 'btn btn-small confirm-delete btn-danger',
          'data-id' => $studio->sid,
        )) ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  </table>

  <div id="modal-delete-studio" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="model-delete-label">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="model-delete-label">Delete a studio</h3>
    </div>
    <div class="modal-body">
      <p>You are about to delete a studio, this procedure is irreversible.</p>
      <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
      <?php echo anchor('admin/studios', 'Yes', array('class' => 'btn btn-danger')); ?>
      <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
    </div>
  </div>

<?php else: ?>
  <p>There's currently no studio. Please click <strong>Add studio</strong> button above to continue.</p>
<?php endif; ?>
