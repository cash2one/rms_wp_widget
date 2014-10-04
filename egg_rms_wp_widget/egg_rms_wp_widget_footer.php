<?php 

function wp_egg_rms_recommand_footer($content){
global $post;  
$pt=strip_tags($post->post_content);
$pt=urlencode(substr($pt,0,900));
$wp_egg_rms_number = get_option("EGG_NUMBER_OF_RMS");
$wp_egg_rms_date_range = get_option("EGG_DATE_RANGE_OF_RMS");

$url="http://106.185.30.33:3000/showwidget?index=theegg&type=article&domain=cosmopolitan.com.hk&type&content=".$pt."&maxc=".$wp_egg_rms_number;
if($wp_egg_rms_date_range>1){
   $url=$url."&range=".$wp_egg_rms_date_range;
}
$iframe="<iframe style='width:600px;height:200px;border:none;' src=".$url."></iframe>";

		return $content."Maybe You Like These Articles:".$iframe;
	//return $content."Maybe You Like These Articles:";
}

add_filter('the_content', 'wp_egg_rms_recommand_footer');


?>