<?php

/**
 * Implements hook_help().
 *
 * Displays help and module information.
 *
 * @param path 
 *   Which path of the site we're using to display help
 * @param arg 
 *   Array that holds the current path as returned from arg() function
 */
function eggrms_help($path, $arg) {
  switch ($path) {
    case "admin/help#eggrms":
      return '<p>' . t("The Egg Recommand System Module") . '</p>';
      break;
  }
} 


/**
 * Custom content function. 
 * 
 * Set beginning and end dates, retrieve posts from database
 * saved in that time period.
 * 
 * @return 
 *   A result set of the targeted posts.
 */
function eggrms_contents(){
  //Get today's date.
  $today = getdate();
  //Calculate the date a week ago.
  $start_time = mktime(0, 0, 0,$today['mon'],($today['mday'] - 7), $today['year']);
  //Get all posts from one week ago to the present.
  $end_time = time();

  //Use Database API to retrieve Egg recommand articles.
  $query = db_select('node', 'n')
    ->fields('n', array('nid', 'title', 'created'))
    ->condition('status', 1) //Published.
    ->condition('created', array($start_time, $end_time), 'BETWEEN')
    ->orderBy('created', 'DESC') //Most recent first.
    ->execute(); 
  return $query;  
}

/**
 * Implements hook_block_info().
 */
function eggrms_block_info() {
  $blocks['eggrms'] = array(
    // The name that will appear in the block list.
    'info' => t('Egg Recommand Articles'),
    // Default setting.
    'cache' => DRUPAL_NO_CACHE,
	'body[format]'=>'full_html',
  );
  return $blocks;
}

/**
 * Implements hook_block_view().
 * 
 * Prepares the contents of the block.
 */
function eggrms_block_view($delta = '') {
  
	$block=array();
	if(arg(0)=='node' && is_numeric(arg(1))){
		$block['subject'] = '';//t('Recommand Articles');
		$nid=arg(1);
		$node=node_load($nid);
		$content=print_r($node->body['und'][0]['value'],true);
		$title=print_r($node->title,true);
		$wp_egg_rms_number = variable_get("EGG_NUMBER_OF_RMS",5);
		$wp_egg_rms_domain=variable_get("EGG_DOMAIN_OF_RMS",'106.185.30.33:3000');
		$wp_egg_rms_date_range = variable_get("EGG_DATE_RANGE_OF_RMS",0);
		$url="http://".$wp_egg_rms_domain."/showwidget?index=theegg&type=article&domain=cosmopolitan.com.hk&maxc=".$wp_egg_rms_number;
		if($wp_egg_rms_date_range>1){
		   $url=$url."&range=".$wp_egg_rms_date_range;
		}
	
		$block['content'] =theme('eggrms_footer',array('url'=>$url,'title'=>$title,'content'=>$content));
		//$block['content']=strip_tags(buildfooter1($url,$content,$title));
	   //print_r("dee<sr");
	  }  
	 
    return $block;
 }
 //implementation of hook_theme
function eggrms_theme() {
  return array(
    'eggrms_footer' => array(
      'variables' => array('url'=>NULL,'title'=>NULL,'content'=>NULL),
	  'path'=>drupal_get_path('module', 'eggrms'),
      'template' => 'eggrms_footer',
	  'render element' => 'content'
    ),
  );
}

/**
 * Implements hook_permission().
 */
function eggrms_permission() {
  return array(
    'access eggrms content' => array(
      'title' => t('Access content for the Egg Rms module'),
    )
  );
}
require_once(dirname(__FILE__).'/egg_rms_drupal_configuration.php');
?>