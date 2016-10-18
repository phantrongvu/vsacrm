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

<p>
<strong>Student: </strong><?php echo $note->student->first_name . ' ' . $note->student->last_name . ' (' . $note->student->mail . ')'; ?>
</p>

<p><strong>Date: </strong><?php echo date('d/m/Y g:ia', $note->created); ?></p>

<div>
  <strong>Note</strong><br />
  <?php echo $this->typography->auto_typography($note->body); ?>
</div>


<div class="form-actions">
  <?php echo anchor('note/manage/' . $note->nid . '/edit', 'Edit', 'class="btn btn-primary"'); ?>
  <?php echo anchor('note/search/' . $note->sid, 'Back', array('class' => 'btn')); ?>
</div>

