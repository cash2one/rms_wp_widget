<?php

/**
 * Implements hook_menu().
 */
function upstoryrms_menu() {
  $items = array();

  $items['admin/config/content/upstoryrms'] = array(
    'title' => 'Upstory Recommender System Setting',
    'description' => 'Configuration for Upstory recommand articles module',
    'page callback' => 'drupal_get_form',
    'page arguments' => array('upstoryrms_form'),
    'access arguments' => array('access administration pages'),
    'type' => MENU_NORMAL_ITEM,
  );
   $items['admin/people/permissions/upstoryrms'] = array(
        'title' => 'Upstory Rms',
        'page callback' => '_upstoryrms_page',
        'access arguments' => array('access upstoryrms content'),
        'type' => MENU_NORMAL_ITEM, 
      );

  return $items;
}

/**
 * Page callback: Upstory recommand articles settings
 *
 * @see upstoryrms_menu()
 */
function upstoryrms_form($form, &$form_state) {
  $form['EGG_NUMBER_OF_RMS'] = array(
    '#type' => 'select',
    '#title' => t('EGG_NUMBER_OF_RMS'),
	 '#options' => array(         
         3 => t('3 articles'),
		 4 => t('4 articles'),
         5 => t('5 articles'),
		 6 => t('6 articles'),
		 7 => t('7 articles'),
       
	   ),
    '#default_value' => variable_get('EGG_NUMBER_OF_RMS', 3),
 
    '#description' => t('The maximum number of articles to display in the block. Range: 3~7'),
    '#required' => TRUE,
  );
    $form['EGG_DATE_RANGE_OF_RMS'] = array(
    '#type' => 'select',
    '#title' => t('EGG_DATE_RANGE_OF_RMS'),
	 '#options' => array(         
         7 => t('7 days'),
		 14 => t('14 days'),
         30 => t('30 days'),
		 0 => t('Unlimited'),
       ),
    '#default_value' => variable_get('EGG_DATE_RANGE_OF_RMS', 0),
    '#description' => t('The date range of articles recommanded.'),
    '#required' => TRUE,
  );
    $form['EGG_RMS_DOMAIN'] = array(
    '#type' => 'textfield',
    '#title' => t('EGG_RMS_DOMAIN'),
    '#default_value' => variable_get('EGG_RMS_DOMAIN', '106.185.30.33:3000'),
	 '#size' => 64,
    '#maxlength' => 256,
    '#description' => t('The egg rms server'),
    '#required' => TRUE,
  );
    $form['EGG_TARGET_DOMAIN'] = array(
    '#type' => 'textfield',
    '#title' => t('EGG_TARGET_DOMAIN'),
    '#default_value' => variable_get('EGG_TARGET_DOMAIN', $_SERVER['HTTP_HOST']),
    '#size' => 64,
    '#maxlength' => 256,
    '#description' => t('your current domain that would be collected'),
    '#required' => TRUE,
  );
    $form['EGG_MAX_POST_NUMBER'] = array(
    '#type' => 'textfield',
    '#title' => t('EGG_MAX_POST_NUMBER'),
    '#default_value' => variable_get('EGG_MAX_POST_NUMBER', 5),
    '#size' => 2,
    '#maxlength' => 2,
    '#description' => t('The maximum number of articles posted to RMS Server per time. Range: 5~20'),
    '#required' => TRUE,
  );
    $form['EGG_RMS_ADVERTISER_ID'] = array(
    '#type' => 'textfield',
    '#title' => t('EGG_RMS_ADVERTISER_ID'),
    '#default_value' => variable_get('EGG_RMS_ADVERTISER_ID', 0),
    '#size' => 2,
    '#maxlength' => 2,
    '#description' => t('the advertiser id generate by RMS'),
    '#required' => TRUE,
  );
	$form['button1'] = array(
    '#type' => 'submit',
    '#value' => 'Reset the Post ID',
    '#submit' => array('upstory_rms_reset_postid')
  );
  return system_settings_form($form);
}
function upstory_rms_reset_postid(){
	$max_post_id=variable_get("EGG_SE_MAX_POST_ID");
	if($max_post_id>0){
		variable_set("EGG_SE_MAX_POST_ID",0);
	}

}
?>