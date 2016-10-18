<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<a href="https://plus.google.com/112026631374905593427" rel="publisher"></a>
<!-- Main hero unit for a primary marketing message or call to action -->
<div id="home_box" class="clearfix">
  <div class="columns col-xs-6">
    <h1>VOX CRM</h1>
    <p>This system is to be used internally among VOX staffs</p>
    <p>Please log-in to continue...</p>
    <p>&nbsp;</p>
  </div>
  <div class="columns col-xs-6">
  	<?php $attributes = array('class' => 'form-horizontal form-login'); ?>
	  <div class="row">
		<?php echo form_open('home', $attributes); ?>
		  <?php if(($errors = validation_errors()) || $login): ?>
			<div class="alert alert-error">
			  <a class="close" data-dismiss="alert">×</a>
			  <?php echo $login; ?>
			  <?php echo $errors; ?>
			</div>
		  <?php endif; ?>
		  <div class="row">
			<div class="columns col-xs-12 col-sm-4 form-label"><?php echo form_label('Email', 'mail', array('class' => '')); ?></div>
			<div class="columns col-xs-12 col-sm-6">
			  <?php echo form_input('mail', set_value('mail'), 'id="mail" placeholder="someone@example.com"'); ?>
			</div>
		  </div>
		  <div class="row">
			<div class="columns col-xs-12 col-sm-4 form-label"><?php echo form_label('Password', 'pass', array('class' => '')); ?></div>
			<div class="columns col-xs-12 col-sm-6">
			  <?php echo form_password('pass', '', 'id="pass" placeholder="password"'); ?>
			</div>
		  </div>
		  <div id="login" class="columns col-xs-12">
			<?php echo form_submit('submit', 'Sign in', 'class="btn btn-primary"'); ?>
		  </div>
		  <div class="clearfix"></div>
		<?php echo form_close(); ?>
	  </div>
  </div>
</div>