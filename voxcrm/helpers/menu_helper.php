<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 27/12/12
 * Time: 10:34 AM
 * To change this template use File | Settings | File Templates.
 */

if ( ! function_exists('menu_dropdown'))
{
	function menu_dropdown($menu)
	{
    //$output = '<div class="nav-collapse collapse"><ul class="nav">';
    $output = '';
	$ctr1 = 0;
	$ctr2 = 0;
    foreach($menu as $link => $title)
    {
      if(is_array($title))
      {
		$ctr1++;
        $output .= '<li class="has-sub '.( $ctr1 >= count($menu) ? 'last' : '' ).'">';
        $output .= anchor(
          $title['item']['link'],
          $title['item']['title']
        );

        $output .= '<ul class="">';
		$ctr2 = 0;
        foreach($title['children'] as $child_link => $child_title)
        {
		  $ctr2++;
          switch($child_link)
          {
            case 'divider':
              $output .= '<li class="divider"></li>';
              break;
            case 'nav-header':
              $output .= '<li class="nav-header">'.$child_title.'</li>';
              break;
            default:
              $output .= '<li '.(uri_string() === $child_link ? 'class="active"' : '').' class="'.( $ctr2 >= count($title['children']) ? 'last' : '' ).'">'.anchor($child_link, $child_title).'</li>';
          }

        }
        $output .= '</ul>';
        $output .= '</li>';
      }
      else
      {
        $output .= '<li '.(uri_string() === $link ? 'class="active"' : '').'>'.anchor($link, $title).'</li>';
      }
    }

   // $output .= '</ul></div>';
  // $output .= '</ul>';

    return $output;
  }
}
