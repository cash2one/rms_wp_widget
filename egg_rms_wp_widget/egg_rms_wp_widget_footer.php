<?php 

function wp_egg_rms_recommand_footer($content){
if(!is_single())
	return $content;
global $post;  
//$pt=strip_tags($post->post_content);
$title=$post->post_title;
$wp_egg_rms_number = get_option("EGG_NUMBER_OF_RMS");

$wp_egg_rms_date_range = get_option("EGG_DATE_RANGE_OF_RMS");
$wp_egg_rms_domain='106.185.30.33:3000';
if(!empty(get_option("EGG_DOMAIN_OF_RMS"))){
$wp_egg_rms_domain=get_option("EGG_DOMAIN_OF_RMS");
}
$wp_egg_target_domain = get_option("EGG_TARGET_DOMAIN");
if(empty($wp_egg_target_domain)){
	$wp_egg_target_domain=$_SERVER["HTTP_HOST"];
}
$url="http://".$wp_egg_rms_domain."/showwidget?index=theegg&type=article&domain=".$wp_egg_target_domain."&maxc=".$wp_egg_rms_number;
if($wp_egg_rms_date_range>1){
   $url=$url."&range=".$wp_egg_rms_date_range;
}
$iframe=buildiframe($url,$content,$title);

		return $content.$iframe;

}

function buildiframe($url,$content,$title){

	$iframe="<iframe style='width:600px;height:200px;border:none;' id='egg_rms_iframe' onload='submit();' src='about:blank'>";
	$form="<form action='".$url."' method='post' target='_self' id='postData_form'>";
	$form.="<input id='_rms_content' name='content' type='hidden' value='".htmlspecialchars(trim(strip_tags($content)))."'/>";
	$form.="<input id='_rms_title' name='title' type='hidden' value='".htmlspecialchars(trim(strip_tags($title)))."'/>";
	$form.="</form>";
	//$iframe.="<script >function submit(){document.getElementById('postData_form').submit(); }</script>";
	
	$iframe.="</iframe>";
	$iframe.="<script > \n";
	$iframe.="document.getElementById('egg_rms_iframe').contentWindow.document.write(\"{$form}\");";
	$iframe.="document.getElementById('egg_rms_iframe').contentWindow.document.getElementById('postData_form').submit(); \n";
	$iframe.="</script>";
	return $iframe;
}

add_filter('the_content', 'wp_egg_rms_recommand_footer');


?>