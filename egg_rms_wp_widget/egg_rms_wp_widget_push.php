<?php 
error_reporting(E_ALL);
ini_set( 'display_errors', 1); 
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

add_action( 'save_post', 'push_on_save', 10 );
add_action( 'update_post', 'push_on_save' );
add_action( 'publish_post', 'push_on_save' );
add_action( 'delete_post', 'push_on_delete' );
add_action( 'trash_post', 'push_on_delete' );

function egg_push_function(){
error_log("start:");
	
	$EGG_MAX_POST_NUMBER=get_option("EGG_MAX_POST_NUMBER");
	if(empty($EGG_MAX_POST_NUMBER) || $EGG_MAX_POST_NUMBER<1 || $EGG_MAX_POST_NUMBER>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	error_log("egg_push_function list:");
	global $wpdb;
	$maxrow=$wpdb->get_results( "SELECT max(id) as max_postid FROM $wpdb->posts" );
	//error_log("maxrow: ".$maxrow[0]->max_postid);
	
	$NewestID=0;
	if(count($maxrow)>0){
		$NewestID=$maxrow[0]->max_postid;
	}
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
	
	error_log("curren ID: ".$CurrentID."  NewestID:".$NewestID);
	//return;
	$Posts=array();
	if($NewestID>0 && $EGG_SE_MAX_POST_ID<$NewestID){

		$count=0;
		for($i=0;$count<$EGG_MAX_POST_NUMBER && $NewestID>=$CurrentID;$i++){
			$p=get_post($CurrentID);
			if(empty($p) || $p->post_status!='publish'){
				$CurrentID+=1;
				continue;
			}else{
			
			}
			
			$a=format_article($p,$wp_egg_target_domain);
			//array("content"=>htmlspecialchars(trim(strip_tags($p->post_content))),"contenttitle"=>$p->post_title,"article_time"=>strtotime($p->post_date),"post_id"=>$CurrentID,"domain"=>$wp_egg_target_domain,"url"=>get_permalink($CurrentID));
		
			array_push($Posts,$a);	
			$count+=1;
			$CurrentID+=1;
		}		
		error_log("post: count. ".$count);
		//error_log("post data: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxd".json_encode($Posts).'current id:'.$CurrentID);
		if(count($Posts)<=0){
			error_log("no article to posts~");
			return;
		}else{
			set_max_post_id($CurrentID-1);
		
		}
		$post_data=array("posts"=>json_encode($Posts),"authdomain"=>$wp_egg_target_domain);
		//error_log($post_data);
		
		$post_url="http://".$postdomain."/pluginpost";
		error_log($posturl);
		//post
		post_articles($post_data,$post_url);
		error_log($output);	
		//error_log($Posts);
	}
}

function post_articles($post_data,$post_url){
	$ch = curl_init();
		/*$header=array(
			'Content-Type: application/x-www-form-urlencoded'.//json',
			';Content-Length:' . strlen($data_string).
			';Charset=UTF-8');*/
		//curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt ($ch, CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_data));
		$output = curl_exec($ch);
		curl_close($ch);
		error_log($output);	
}
function push_on_save($post_id){
	error_log("  current update post id:".$post_id." updated:".$updated);
	echo "post on save";
	$post=get_post($post_id);
	if($post->post_status!='publish'){
	    echo "post on save but not publish";
		return;
	}
		
	$EGG_MAX_POST_NUMBER=get_option("EGG_MAX_POST_NUMBER");
	if(empty($EGG_MAX_POST_NUMBER) || $EGG_MAX_POST_NUMBER<1 || $EGG_MAX_POST_NUMBER>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	
	$postdomain=get_option("EGG_RMS_DOMAIN");
	if(empty($postdomain)){
		$postdomain="106.185.30.33:3000";			
	}
	$wp_egg_target_domain = get_option("EGG_TARGET_DOMAIN");
	if(empty($wp_egg_target_domain)){
		$wp_egg_target_domain=$_SERVER["HTTP_HOST"];
	}
	$EGG_SE_MAX_POST_ID=get_max_post_id($postdomain,$wp_egg_target_domain);
	
	if(empty($EGG_SE_MAX_POST_ID)){
		$EGG_SE_MAX_POST_ID=0;
	}
	$Posts=array();
	if($post_id>$EGG_SE_MAX_POST_ID){ return;}
	$p=get_post($post_id);
	if(!empty($p)){
		array_push($Posts,format_article($p,$wp_egg_target_domain));
	}
	if(count($Posts)<=0){
		error_log("no article to posts~");
		return;
	}
	$post_data=array("posts"=>json_encode($Posts),"authdomain"=>$wp_egg_target_domain);
	error_log($post_data);
	
	$post_url="http://".$postdomain."/pluginpost";
	error_log($post_url);
	//post
	post_articles($post_data,$post_url);
	error_log($output);	
		//error_log($Posts);
	
}
function push_on_delete($postId) { 
   $postdomain=get_option("EGG_RMS_DOMAIN");
	if(empty($postdomain)){
		$postdomain="106.185.30.33:3000";			
	}
	$post_url="http://".$postdomain."/delete_cms_article";
	$wp_egg_target_domain = get_option("EGG_TARGET_DOMAIN");
	if(empty($wp_egg_target_domain)){
		$wp_egg_target_domain=$_SERVER["HTTP_HOST"];
	}
	$post_data=array("authdomain"=>$wp_egg_target_domain,"targetdomain"=>$wp_egg_target_domain,"url"=>get_permalink($postId));
	post_articles($post_data,$post_url);
}

function format_article($p,$wp_egg_target_domain){
	if(empty($p)){return array();}
	$first_img=catch_first_image($p->post_content);
	$a= array("content"=>htmlspecialchars(trim(strip_tags($p->post_content))),"contenttitle"=>$p->post_title,"article_time"=>strtotime($p->post_date),"post_id"=>$p->ID,"domain"=>$wp_egg_target_domain,"url"=>get_permalink($p->ID),"thumbnail"=>$first_img);
	return $a;
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
		error_log("REMOTE MaxID:".$output);
		if(!empty($ret) && $ret->error_code==0 ){
			if($ret->ret_value>=$EGG_SE_MAX_POST_ID || $ret->ret_value>1){
				$EGG_SE_MAX_POST_ID=$ret->ret_value;
			}
			if($ret->ret_value==-1){
				$EGG_SE_MAX_POST_ID=0;
			}
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
