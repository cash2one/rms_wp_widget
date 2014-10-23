<?php  
//error_log(time()."   ".current_time("timestamp"));
//	wp_clear_scheduled_hook("egg_rms_push_article");
//	error_log("remove scheduled");
if(!wp_next_scheduled("egg_rms_push_article")){
 wp_schedule_event(time(), "hourly", "egg_rms_push_article" );
 add_action("egg_rms_push_article", "egg_push_function");
 error_log("add scheduled");
 
}else{
//	wp_clear_scheduled_hook("egg_rms_push_article");
//	error_log("remove scheduled");
}

//error_log($_SERVER);
//error_log("".json_encode(wp_get_schedules()));
function egg_push_function(){
error_log("start:");
	
	$EGG_MAX_POST_NUMBER=get_option("EGG_MAX_POST_NUMBER");
	if(empty($EGG_MAX_POST_NUMBER) || $EGG_MAX_POST_NUMBER<1 || $EGG_MAX_POST_NUMBER>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	error_log("egg_push_function list:");
	$recent_posts=wp_get_recent_posts( array("numberposts"=>1) ) ;
	error_log("recent_posts".json_encode($recent_posts));
	
	
	$NewestID=$recent_posts[0]["ID"];
	$wp_egg_target_domain = get_option("EGG_TARGET_DOMAIN");
	if(empty($wp_egg_target_domain)){
		$wp_egg_target_domain=$_SERVER["HTTP_HOST"];
	}
	$postdomain=get_option("EGG_RMS_DOMAIN");
		if(empty($postdomain)){
			$postdomain="106.185.30.33:3000";			
		}
	$EGG_SE_MAX_POST_ID=get_max_post_id($postdomain,$wp_egg_target_domain);
	
	if(empty($EGG_SE_MAX_POST_ID)){
		$EGG_SE_MAX_POST_ID=0;
	}
	$CurrentID=$EGG_SE_MAX_POST_ID+1;
	
	error_log("Max ID: ".$EGG_SE_MAX_POST_ID."  Recent POSTS:".count($recent_posts));
	error_log("Max ID: ".$EGG_SE_MAX_POST_ID."  NewestID:".$recent_posts[0]["ID"]);
	//return;
	$Posts=array();
	if(count($recent_posts)>0 && $EGG_SE_MAX_POST_ID<$recent_posts[0]["ID"]){

		$count=0;
		for($i=0;$count<$EGG_MAX_POST_NUMBER && $NewestID>=$CurrentID;$i++){
			$p=get_post($CurrentID);
			if(empty($p) || $p->post_status!='publish'){
				$CurrentID+=1;
				continue;
			}else{
			
			}
			$first_img=catch_first_image($p->post_content);
			$a=array("content"=>htmlspecialchars(trim(strip_tags($p->post_content))),"contenttitle"=>$p->post_title,"article_time"=>strtotime($p->post_date),"post_id"=>$CurrentID,"domain"=>$wp_egg_target_domain,"url"=>get_permalink($CurrentID));
			//$a=array("content"=>htmlspecialchars(trim(strip_tags($p->post_content))),"contenttitle"=>$p->post_title,"article_time"=>strtotime($p->post_date),"post_id"=>$CurrentID,"domain"=>$wp_egg_target_domain,"url"=>get_permalink($CurrentID));
			array_push($Posts,$a);	
			$count+=1;
			$CurrentID+=1;
		}		
		//error_log("post data: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxd".json_encode($Posts).'current id:'.$CurrentID);
		if(count($Posts)<=0){
			error_log("no article to posts~");
			return;
		}else{
			set_max_post_id($CurrentID-1);
		
		}
		$post_data=array("posts"=>json_encode($Posts),"authdomain"=>$wp_egg_target_domain);
		//error_log($post_data);
		
		$posturl="http://".$postdomain."/pluginpost";
		error_log($posturl);
		$ch = curl_init();
		/*$header=array(
			'Content-Type: application/x-www-form-urlencoded'.//json',
			';Content-Length:' . strlen($data_string).
			';Charset=UTF-8');*/
		//curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt ($ch, CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_data));
		$output = curl_exec($ch);
		curl_close($ch);
		error_log($output);	
		//error_log($Posts);
	}
}
function get_max_post_id($rmsdomain,$targetdomain){
		$EGG_SE_MAX_POST_ID=get_option("EGG_SE_MAX_POST_ID");
		$ch = curl_init();
		$url="http://".$rmsdomain."/maxpostid?domain=".$targetdomain;		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$header=array(
			'Content-Type: text/json',
			'Charset=UTF-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt ($ch, CURLOPT_HEADER,false);
		$output = curl_exec($ch);
		curl_close($ch);
		$ret=json_decode($output);
		error_log("MaxID:".$output);
		if(!empty($ret) && $ret->error_code==0 && $ret->ret_value>=$EGG_SE_MAX_POST_ID){
			$EGG_SE_MAX_POST_ID=$ret->ret_value;
			update_option("EGG_SE_MAX_POST_ID",$EGG_SE_MAX_POST_ID);
		}
		return $EGG_SE_MAX_POST_ID;


}
function set_max_post_id($mid){
	update_option("EGG_SE_MAX_POST_ID",$mid);
}

function catch_first_image($content){
    $first_img='';
    ob_start();
    ob_end_clean();
    $output=preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',$content,$matches);
    $first_img=$matches[1][0];
    if(empty($first_img)){//
		$first_img='';
    }
    return $first_img;
}
?>