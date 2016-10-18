<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<a href="https://plus.google.com/112026631374905593427" rel="publisher"></a>
<!--<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="<?php echo base_url(); ?>">VOX CRM</a>
			<?php echo menu_dropdown($menu); ?>
			<div class="nav-collapse pull-right">
				<span class="navbar-text">
					Welcome <?php echo anchor('people/staff/' . $user->uid, $user->first_name); ?>&nbsp;&nbsp;
				</span>
				<?php echo anchor('logout', 'Logout', array('class' => 'btn btn-mini btn-info', 'style' => 'margin-top: 0;')); ?>
			</div>
		</div>
	</div>
</div>-->
<div id="cssmenu_container">
	<div class="container">
		<div id='cssmenu'>
			<ul>
				<li><a class="brand" href="<?php echo base_url(); ?>">VOX CRM</a></li>
				<?php echo menu_dropdown($menu); ?>
				<!--<li class='has-sub'><a href='#'><span>Calendar</span></a>
					<ul>
						<li><a href='#'><span>Add Event</span></a></li>
						<li class='last'><a href='#'><span>View Calendar</span></a></li>
					</ul>
				</li>
				<li class='has-sub'><a href='#'><span>People</span></a>
					<ul>
						<li><a href='#'><span>Search</span></a></li>
						<li><a href='#'><span>Add Student</span></a></li>
						<li class='last'><a href='#'><span>Add Staff</span></a></li>
					</ul>
				</li>
				<li class='has-sub'><a href='#'><span>Settings</span></a>
					<ul>
						<li><a href='#'><span>Manage Studios</span></a></li>
						<li><a href='#'><span>Manage Products</span></a></li>
						<li class='last'><a href='#'><span>Manage Permissions</span></a></li>
					</ul>
				</li>
				<li class='has-sub last'><a href='#'><span>Reports</span></a>
					<ul>
						<li><a href='#'><span>Weekly Timesheet</span></a></li>
						<li><a href='#'><span>Teacher Performance</span></a></li>
						<li><a href='#'><span>Sale Performance</span></a></li>
						<li class='last'><a href='#'><span>Sale Forecast</span></a></li>
					</ul>
				</li>-->
				<span id="logout">
					Welcome <?php echo anchor('people/staff/' . $user->uid, $user->first_name); ?>
					<?php echo anchor('logout', 'Logout', array('class' => 'btn btn-mini btn-info', 'style' => 'margin-top: 0; margin-left: 14px;')); ?>
				</span>
			</ul>
		</div>
	</div>
</div>