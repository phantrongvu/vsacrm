<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 27/12/12
 * Time: 3:03 PM
 * To change this template use File | Settings | File Templates.
 */

$config = array(
  'menu_admin' => array(
    'people' => array(
      'item' => array('link' => '', 'title' => 'People'),
      'children' => array(
        'people/search/student' => 'Search',
        'people/student' => 'Add student',
        'people/staff' => 'Add staff',
      )
    ),
    'calendar' => array(
      'item' => array('link' => '', 'title' => 'Calendar'),
      'children' => array(
        'calendar/event' => 'Add event',
        'calendar' => 'View calendar'
      )
    ),
    'settings' => array(
      'item' => array('link' => '', 'title' => 'Settings'),
      'children' => array(
        'admin/studios' => 'Manage studios',
        'admin/products' => 'Manage products',
        'admin/permissions' => 'Manage permissions'
      )
    ),
    'reports' => array(
      'item' => array('link' => '', 'title' => 'Reports'),
      'children' => array(
        'report/timesheet' => 'Weekly timesheet',
		'report/log_reports' => 'Log Reports',
        'report/teacher_performance' => 'Teacher performance',
        'report/sale_performance' => 'Sale performance',
        'report/sale_forecast' => 'Sale forecast',
      ),
    ),
  ),
  'menu_teacher' => array(
    'calendar' => 'Calendar',
  ),
);