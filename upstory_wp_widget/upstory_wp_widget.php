<?php
/**
 * @package upstory_WP_Widget
 * @version 1.6
 */
/*
Plugin Name: UPSTORY wordpress widget
Plugin URI: http://www.theegg.com
Description: This is a widget for upstory system. including a setting and a related article list 
Author: Vincent.leung.cn@upstory
Version: 0.1
*/
define( 'UPSTORY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//add setting menu
require_once (UPSTORY__PLUGIN_DIR."upstory_wp_widget_setting.php");
require_once (UPSTORY__PLUGIN_DIR."upstory_wp_widget_footer.php");
require_once (UPSTORY__PLUGIN_DIR."upstory_wp_widget_push.php");




?>
