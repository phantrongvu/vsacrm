<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 20/01/13
 * Time: 11:42 AM
 * To change this template use File | Settings | File Templates.
 */
?>

<p>&nbsp;</p>
<?php echo anchor('note/manage?sid=' . $this->uri->segment(3), 'Add Notes', array('class' => 'btn btn-primary')); ?>
<p>&nbsp;</p>

<?php if( ! empty($notes) && count($notes)): ?>
<table class="table table-striped table-hover table-bordered">
  <thead>
  <tr>
    <th>Title</th>
    <th>Message</th>
    <th>Student</th>
    <th>Date</th>
    <th>Actions</th>
  </tr>
  </thead>
  <tbody>

    <?php foreach($notes as $note): ?>
  <tr>
    <td><?php echo anchor('note/view/' . $note->nid, $note->title); ?></td>
    <td><?php echo $this->typography->auto_typography($note->body); ?></td>
    <td><?php echo $note->student->first_name . ' ' . $note->student->last_name . ' (' . $note->student->mail . ')'; ?></td>
    <td><?php echo date('d/m/Y g:ia', $note->created); ?></td>
    <td>
      <?php echo anchor('note/manage/' . $note->nid . '/edit', 'Edit', array('class' => 'btn btn-small')) ?>
      <?php echo anchor('note/manage/' . $note->nid . '/delete?uid=' . $note->sid, 'Delete',
      array(
        'class' => 'btn btn-small confirm-delete btn-danger',
        'data-id' => $note->nid,
        'data-sid' => $note->sid,
      )) ?>
    </td>
  </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php echo $pagination; ?>

<div id="modal-delete-note" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="model-delete-label">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="model-delete-label">Delete a note</h3>
  </div>
  <div class="modal-body">
    <p>You are about to delete a note, this procedure is irreversible.</p>
    <p>Do you want to proceed?</p>
  </div>
  <div class="modal-footer">
    <?php echo anchor('note/delete', 'Yes', array('class' => 'btn btn-danger')); ?>
    <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
  </div>
</div>

<?php else: ?>
  <p>There's currently no note.</p>
<?php endif; ?>
