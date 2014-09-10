<?php
/**
 * @package EGG_RMS_WP_Widget
 * @version 1.6
 */
/*
Plugin Name: Egg RMS wordpress widget
Plugin URI: http://www.theegg.com
Description: This is a widget for Egg's recommander system. including a setting and a related article list 
Author: Vincent.leung@Theegg
Version: 0.1
*/
define( 'EGGRMS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//add setting menu
require_once (EGGRMS__PLUGIN_DIR."egg_rms_wp_widget_setting.php");
?>
