<?php  
//var_dump(time()."   ".current_time("timestamp"));
	wp_clear_scheduled_hook("egg_rms_push_article");
	error_log("remove scheduled");
if(!wp_next_scheduled("egg_rms_push_article")){
 wp_schedule_event(time(), "hourly", "egg_rms_push_article" );
 add_action("egg_rms_push_article", "egg_push_function");
 error_log("add scheduled");
 
}else{
//	wp_clear_scheduled_hook("egg_rms_push_article");
//	error_log("remove scheduled");
}

//var_dump($_SERVER);
//error_log("".json_encode(wp_get_schedules()));
error_log("out:");
function egg_push_function(){
error_log("start:");
	$EGG_SE_MAX_ID=get_option("EGG_SE_MAX_ID");
	if(empty($EGG_SE_MAX_ID)){
		$EGG_SE_MAX_ID=0;
	}
	$EGG_MAX_POST_NUMBER=get_option("EGG_MAX_POST_NUMBER");
	if(empty($EGG_MAX_POST_NUMBER) || $EGG_MAX_POST_NUMBER<1 || $EGG_TARGET_DOMAIN>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	var_dump("egg_push_function list:");
	$recent_posts=wp_get_recent_posts( array("numberposts"=>1) ) ;
	error_log("recent_posts".json_encode($recent_posts));
	$Posts=array();
	$NewestID=$recent_posts[0]["ID"];
	$CurrentID=$EGG_SE_MAX_ID+1;
	$wp_egg_rms_domain = get_option("EGG_TARGET_DOMAIN");
	if(empty($wp_egg_rms_domain)){
		$wp_egg_rms_domain=$_SERVER["HTTP_HOST"];
	}
	error_log("Max ID: ".$EGG_SE_MAX_ID."  Recent POSTS:".count($recent_posts));
	if(count($recent_posts)>0 && $EGG_SE_MAX_ID<$recent_posts[0]["ID"]){

		$count=0;
		for($i=0;$count<$EGG_MAX_POST_NUMBER && $NewestID>=$CurrentID;$i++){
			$p=get_post($CurrentID);
			if(empty($p) || $p->post_status!='publish'){
				$CurrentID+=1;
				continue;
			}else{
			
			}
			$first_img=catch_first_image($p->post_content);
			$a=array("content"=>htmlspecialchars(trim(strip_tags($p->post_content))),"contenttitle"=>$p->post_title,"article_time"=>strtotime($p->post_date),"post_id"=>$CurrentID,"domain"=>$wp_egg_rms_domain,"url"=>get_permalink($CurrentID));
			array_push($Posts,$a);	
			$count+=1;
			$CurrentID+=1;
		}
		error_log(json_encode($Posts));
		var_dump($Posts);
	}
}

function catch_first_image($content){
    $first_img='';
    ob_start();
    ob_end_clean();
    $output=preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',$content,$matches);
    $first_img=$matches[1][0];
    if(empty($first_img)){
		//
		$first_img='';
    }
    return $first_img;
}
?>