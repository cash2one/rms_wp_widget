<?php

function buildfooter($title,$content){
 
	$upstory_rms_number = variable_get("EGG_NUMBER_OF_RMS",5);
	$upstory_rms_domain=variable_get("EGG_DOMAIN_OF_RMS",'106.185.30.33:3000');
	$upstory_rms_date_range = variable_get("EGG_DATE_RANGE_OF_RMS",0);
	$upstory_rms_target_domain=variable_get("EGG_TARGET_DOMAIN","cosmopolitan.com.hk");
	$url="http://".$upstory_rms_domain."/showwidget?index=theegg&type=article&domain=".$upstory_rms_target_domain."&maxc=".$upstory_rms_number;
	if($upstory_rms_date_range>1){
	   $url=$url."&range=".$upstory_rms_date_range;
	}
	return buildiframe($url,$content,$title);

	//return $iframe;

}
function buildiframe($url,$content,$title){

	//print_r("233< div > ");
	$content=htmlspecialchars(trim(strip_tags($content)));
	$content=str_replace(PHP_EOL,"",$content);
	$content=str_replace('\''," ",$content);
	$title=str_replace('\''," ",$title);
	$iframe="<iframe style='width:600px;height:200px;border:none;' id='upstory_rms_iframe' onload='submit();' src='about:blank'>";
	$form="<form action=\"".$url."\" method=\"post\" target=\"_self\" id=\"postData_form\">";
	$form.="<input id=\"_rms_content\" name=\"content\" type=\"hidden\" value=\"".htmlspecialchars(trim(strip_tags($content)))."\"/>";
	$form.="<input id=\"_rms_title\" name=\"title\" type=\"hidden\" value=\"".htmlspecialchars(trim(strip_tags($title)))."\"/>";
	$form.="</form>";
	
	
	$iframe.="</iframe>";
	$script="";
	//$script.="alert(123);\n";
	$script.="document.getElementById('upstory_rms_iframe').contentWindow.document.write('{$form}');";
	$script.="document.getElementById('upstory_rms_iframe').contentWindow.document.getElementById('postData_form').submit(); \n";
	//$script.="alert(3123);\n";
	$script.="";
	drupal_add_js($script,array('type' => 'inline','scope' => 'footer','weight' =>5));
	//return $iframe;
	return $iframe;
}

?>

<?php print buildfooter($title,$content); ?>