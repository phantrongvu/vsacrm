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
<p><strong>Total revenue (<?php print date('d/m/y', $start) ?> - <?php print date('d/m/y', $end) ?>): AUD <?php print number_format($sale_forecast, 2) ?></strong></p>
