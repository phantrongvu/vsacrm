<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<fieldset>
  <div id="widget">
    <div id="widgetField">
      <span>28 July, 2008 &divide; 31 July, 2008</span>
      <a href="#">Select date range</a>
    </div>
    <div id="widgetCalendar">
    </div>
  </div>
</fieldset>

<p>&nbsp;</p>

<table class="table table-bordered table-striped responsive" style="max-width: 450px;">
  <thead>
    <tr>
      <th colspan="2">Sale status</th>
      <th>Count</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="2">Sales pending</td>
      <td><?php print $data['sales_pending'] ?></td>
    </tr>
    <tr>
      <td colspan="2">Sales won</td>
      <td><?php print $data['sales_won'] ?></td>
    </tr>
    <tr>
      <td colspan="3">Deletion</td>
    </tr>
    <?php foreach($data['sales_lost'] as $key => $_sales_lost): ?>
      <tr>
        <td></td>
        <td>
          <?php print $this->delete_reason_model->reason_value($key) ?>
          <?php if($this->delete_reason_model->reason_value($key) == 'NON PAYMENT'): ?> (Sales lost)<?php endif; ?>
        </td>
        <td><?php print count($_sales_lost); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
