<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 23/01/13
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<h4>From <?php echo date('d/m/Y', $range['start']); ?> to <?php echo date('d/m/Y', $range['end']); ?></h4>
<fieldset>
  <?php echo form_label('Select teacher', 'teacher_id'); ?>
  <?php echo form_dropdown(
    'teacher_id',
    array('' => 'Please select') + $this->user_model->list_options(),
    $this->uri->segment(3) ?$this->uri->segment(3) : '',
    'id="teacher_id" class="input-xxlarge"'); ?>

  <?php if(isset($week)): ?>
    <br />
    <a href="/report/timesheet/<?php print $this->uri->segment(3) ?>?week=<?php print ($week - 1) ?>" class="btn">&lt;&lt; Back</a>
    <a href="/report/timesheet/<?php print $this->uri->segment(3) ?>?week=<?php print ($week + 1) ?>" class="btn">Next &gt;&gt;</a>
  <?php endif; ?>
</fieldset>

<hr />
<?php if( ! empty($teacher)): ?>
  <h2><?php echo $teacher->first_name . ' ' . $teacher->last_name ?></h2>

  <?php $passes_total = $count['passes']; ?>
  <?php $over24_total = $count['over24']; ?>
  <?php $under24_total = $count['under24']; ?>
  <?php $unpaid_total = $count['unpaid']; ?>

  <div class="row">
    <?php foreach($schedules as $status => $status_schedules): ?>
      <div class="columns col-xs-4">
		<div class="well well-small">
			<h4><?php print strtoupper($status); ?></h4>
			<ul>
			  <?php foreach($status_schedules as $day => $_schedules): ?>
				<h5><?php print $day ?></h5>
				<?php foreach($_schedules as $schedule): ?>
				  <li>
					<h6><?php echo $schedule->name ?></h6>
					<ul>
					  <li>Time: <em><?php echo date('d/m/Y g:ia', $schedule->start); ?> - <?php echo date('d/m/Y g:ia', $schedule->end); ?></em></li>
					</ul>
				  </li>
				<?php endforeach; ?>
			  <?php endforeach; ?>
			</ul>
			Total classes (p. 30min): <?php print $count[$status]; ?>
		</div>
      </div>
    <?php endforeach; ?>
  </div>

<div class="row">
  <?php $grand_total = 0; ?>
  <?php $singing_lessons_total = ($passes_total + $under24_total) * $teacher->rate; ?>
  <?php $grand_total = $singing_lessons_total; ?>

  <?php print form_open('report/timesheet/' . ($teacher ? $teacher->uid : '') . '?week=' . $this->input->get('week')); ?>
  <div class="columns col-xs-12">
	  <ul class="summary well well-small">
		<li class="clearfix"><em>Unpaid:</em> <strong><?php echo $unpaid_total ?> class(es)</strong></li>
		<li class="clearfix"><em>Over 24:</em> <strong><?php echo $over24_total ?> class(es)</strong></li>
		<li class="clearfix"><em>Under 24:</em> <strong><?php echo $under24_total ?> class(es)</strong></li>
		<li class="clearfix"><em>Paid:</em> <strong><?php echo $passes_total ?> class(es)</strong></li>
		<li class="clearfix">Total singing lessons paid classes: <strong><?php echo ($passes_total + $under24_total) ?> class(es)</strong></li>

		<?php // calculate availability for this teacher ?>
		<?php if($teacher->availability): ?>
		  <li class="clearfix"><em>Availability</em> <strong><?php print $teacher->availability - 0.5 * ($passes_total + $under24_total) ?> hours</strong></li>
		<?php else : ?>
		  <li class="clearfix"><small><em>Enter number hour hours per week for this teacher (availability field) for availability calculation</em></small></li>
		<?php endif; ?>

		<?php // calculate rate for this teacher ?>
		<li class="clearfix">Teacher rate: <strong><?php print $teacher->rate ?></strong></li>
		<li class="clearfix">Singing lessons total: <strong> AUD <?php print number_format($singing_lessons_total, 2) ?></strong></li>

		<?php if(count($custom_products)): ?>
		  <li class="clearfix custom-products">
			<h4>Custom products:</h4>
			<?php foreach($custom_products as $day => $_custom_products): ?>
			  <ul>
				<h5><?php print $day ?></h5>
				<?php foreach($_custom_products as $schedule): ?>
				  <li>
					<h6><?php echo $schedule->product_name ?></h6>
					<ul>
					  <li class="clearfix">
						<em><?php echo date('d/m/Y g:ia', $schedule->start); ?> - <?php echo date('d/m/Y g:ia', $schedule->end); ?></em>
						<strong>
						  <input type="text"
								 placeholder="0.00"
								 class="input-small"
								 name="<?php print $schedule->cid ?>"
								 value="<?php print !empty($post[$schedule->cid]) ? $post[$schedule->cid] : '' ?>"
							/>

						  <?php if(!empty($post[$schedule->cid])): ?>
							<?php $grand_total += (float) $post[$schedule->cid] ?>
						  <?php endif; ?>
						</strong>
					  </li>
					</ul>
				  </li>
				<?php endforeach; ?>
			  </ul>
			<?php endforeach; ?>
		  </li>
		<?php endif; ?>

		<li class="clearfix extra-items">
		  <h4>Extra:</h4>
			<ul class="ul-extra-items">
			  <?php if(!empty($post['extra_item_name'])): ?>
				<?php foreach($post['extra_item_name'] as $key => $value): ?>
				  <?php if($post['extra_item_name'][$key] && $post['extra_item_value'][$key]): ?>
					<li class="clearfix">
					<input type="text" placeholder="Item"
						   class="input-medium"
						   name="extra_item_name[]"
						   value="<?php print $post['extra_item_name'][$key]; ?>"
					  />
					<strong><input type="text" placeholder="0.00"
								   class="input-small"
								   name="extra_item_value[]"
								   value="<?php print $post['extra_item_value'][$key]; ?>"
						/></strong>

					  <?php if(!empty($post['extra_item_value'][$key])): ?>
						<?php $grand_total += (float) $post['extra_item_value'][$key] ?>
					  <?php endif; ?>
					</li>
				  <?php endif; ?>
				<?php endforeach; ?>
			  <?php endif; ?>
			</ul>
			<ul>
			  <button class="btn btn-mini btn-add-extra-item">Add more item</button>
			</ul>
		</li>

		<li class="clearfix grand-total">
		  <input type="submit" value="Grand Total" class="btn btn-medium" /><strong><?php print number_format($grand_total, 2) ?></strong>
		</li>
	  </ul>
  </div>
  <?php print form_close(); ?>
</div>
<?php endif; ?>
