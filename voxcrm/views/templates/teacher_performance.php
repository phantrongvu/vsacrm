<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


<fieldset>
  <?php echo form_dropdown(
    'teacher_id',
    array('' => 'Please select teacher') + $this->user_model->list_options(),
    $this->input->get('uid') ? $this->input->get('uid') : '',
    'id="teacher_id" class="input-xxlarge"'); ?>

  <div id="widget">
    <div id="widgetField">
      <span>28 July, 2008 &divide; 31 July, 2008</span>
      <a href="#">Select date range</a>
    </div>
    <div id="widgetCalendar">
    </div>
  </div>

  <div class="btn-toolbar">
    <div class="btn-group">
      <a href="#" class="btn btn-small btn-weekly <?php print $this->input->get('view') == 'week' ? 'active' : '' ?>">Weekly</a>
      <a href="#" class="btn btn-small btn-monthly <?php print $this->input->get('view') == 'month' ? 'active' : '' ?>">Monthly</a>
    </div>
  </div>
</fieldset>

<p>&nbsp;</p>

<?php if($this->input->get('uid')): ?>
  <div>
    <div id="pass_chart_div"></div>
    <div id="o24_chart_div"></div>
    <div id="u24_chart_div"></div>
    <div id="retention_turnover"></div>

    <?php if( ! empty($email_admins)): ?>
      <div id="email_admin" style="max-width: 450px;">
        <h3 style="font-size: 14px; font-weight: bold;">Email admins</h3>
        <small>
          <table class="table table-striped table-bordered responsive">
            <thead>
            <tr>
              <th>Reason</th>
              <th>Count</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($email_admins as $reason => $_email_admins): ?>
              <tr>
                <td><?php print $this->email_admin_reason->reason_value($reason) ?></td>
                <td><?php print count($_email_admins) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </small>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
