<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
function eggrms_cron() {
  // Default to an hourly interval. Of course, cron has to be running at least
  // hourly for this to work.
  $interval = variable_get('eggrms_cron_interval', 60 * 60);
  // We usually don't want to act every time cron runs (which could be every
  // minute) so keep a time for the next run in a variable.
  if (time() >= variable_get('eggrms_cron_next_execution', 0)){
    // This is a silly example of a cron job.
    // It just makes it obvious that the job has run without
    // making any changes to your database.
    watchdog('eggrms_cron', 'eggrms_cron runing');
    //do the push job
	egg_push_function();
    variable_set('eggrms_cron_next_execution', time() + $interval);
  }
  egg_push_function();
}

function egg_push_function(){
error_log("start:");
	
	$EGG_MAX_POST_NUMBER=variable_get("EGG_MAX_POST_NUMBER",0);
	if($EGG_MAX_POST_NUMBER<1 || $EGG_MAX_POST_NUMBER>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	error_log("egg_push_function list:");
	

	//drupal database max nid
	$max_nid=db_query("SELECT max(nid) as maxnid FROM {node} where status=1");
	$NewestID=0;
	foreach ($max_nid as $record) {
		$NewestID=$record->maxnid;
	}
	//watchdog("eggrms_cron","max_id: ".$NewestID );
	//return;
	
	
	
	$wp_egg_target_domain = variable_get("EGG_TARGET_DOMAIN",'');
	if(empty($wp_egg_target_domain)){
		$wp_egg_target_domain=$_SERVER["HTTP_HOST"];
	}
	$postdomain=variable_get("EGG_RMS_DOMAIN",'');
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
			$p=node_load($CurrentID);
			if(empty($p) || $p->status!=NODE_PUBLISHED){
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
		if(count($Posts)<=0){
			error_log("no article to posts~");
			return;
		}else{
			set_max_post_id($CurrentID-1);
		
		}
		$post_data=array("posts"=>json_encode($Posts,JSON_UNESCAPED_UNICODE),"authdomain"=>$wp_egg_target_domain);
		//error_log($post_data);
		
		$post_url="http://".$postdomain."/pluginpost";

		//post
		
		post_articles($post_data,$post_url);
	
		//error_log($Posts);
	}
}

function post_articles($post_data,$post_url){
	$ch = curl_init();
	$data_string=http_build_query($post_data);
	watchdog('eggrms_cron', 'post url:'.$post_url."data:".json_encode($post_data,JSON_UNESCAPED_UNICODE));
		/*$header=array(
			'Content-Type: application/x-www-url-encoded',
			';Content-Length:' . strlen($data_string).
			';Charset=UTF-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt ($ch, CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
		$output = curl_exec($ch);
		curl_close($ch);
		//error_log($output);	
}

function eggrms_node_update($node) {
     // Provides the "updated" values
     //watchdog('eggrms_cron new',json_encode($node,JSON_UNESCAPED_UNICODE));
     //$loaded_node = node_load($node->nid);
     // Provides the old values, which shouldn't be there according
     // to the documentation above
     //watchdog('eggrms_cron old',json_encode($loaded_node,JSON_UNESCAPED_UNICODE));
	 push_on_save($node);
}
function eggrms_node_delete($node){
	$nid=$node->nid;
	$EGG_SE_MAX_POST_ID=variable_get("EGG_SE_MAX_POST_ID",0);
	if($nid>0 && $nid<$EGG_SE_MAX_POST_ID){
	//delete 
		
	
	}


}

function push_on_save($node){
	$post_id=$node->nid;

	if($node->status!=NODE_PUBLISHED){
		return;
	}
		
	$EGG_MAX_POST_NUMBER=variable_get("EGG_MAX_POST_NUMBER",0);
	if($EGG_MAX_POST_NUMBER<1 || $EGG_MAX_POST_NUMBER>20){
		$EGG_MAX_POST_NUMBER=5;
	}
	
	$postdomain=variable_get("EGG_RMS_DOMAIN","106.185.30.33:3000");
	
	$wp_egg_target_domain = variable_get("EGG_TARGET_DOMAIN",$_SERVER["HTTP_HOST"]);
	
	$EGG_SE_MAX_POST_ID=get_max_post_id($postdomain,$wp_egg_target_domain);
	watchdog("eggrms_push_on_save","remote_max_posted_id".$EGG_SE_MAX_POST_ID."  current update post id:".$post_id);
	if(empty($EGG_SE_MAX_POST_ID)){
		$EGG_SE_MAX_POST_ID=0;
	}
	$Posts=array();
	//just post the old one which has been post before.
	if($post_id>$EGG_SE_MAX_POST_ID){ return;}
	if(!empty($node)){
		array_push($Posts,format_article($node,$wp_egg_target_domain));
	}
	if(count($Posts)<=0){
		error_log("no article to posts~");
		return;
	}
	$post_data=array("posts"=>json_encode($Posts,JSON_UNESCAPED_UNICODE),"authdomain"=>$wp_egg_target_domain);	
	$post_url="http://".$postdomain."/pluginpost";
	
	post_articles($post_data,$post_url);

	
}
function format_article($p,$wp_egg_target_domain){
	if(empty($p)|| empty($p->body)){return array();}
	$source_body=$p->body['und'][0]['value'];
	$source_body=str_replace(PHP_EOL,"",$source_body);
	$first_img=catch_first_image($source_body);
	$a= array("content"=>htmlspecialchars(trim(strip_tags($source_body))),"contenttitle"=>$p->title,"article_time"=>$p->created,"post_id"=>$p->nid,"domain"=>$wp_egg_target_domain,"url"=> url('node/'.$p->nid,array('absolute' => TRUE)),"thumbnail"=>$first_img);
	return $a;
}
function get_max_post_id($rmsdomain,$targetdomain){
		$EGG_SE_MAX_POST_ID=variable_get("EGG_SE_MAX_POST_ID",0);
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
			variable_set("EGG_SE_MAX_POST_ID",$EGG_SE_MAX_POST_ID);
		}
		watchdog("eggrms_cron","max se post id:{$EGG_SE_MAX_POST_ID}");
		return $EGG_SE_MAX_POST_ID;


}
function set_max_post_id($mid){
	variable_set("EGG_SE_MAX_POST_ID",$mid);
}

function catch_first_image($content){
    $first_img='';
    ob_start();
    ob_end_clean();
    $output=preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',$content,$matches);
	if(count($matches[1])>0){
		$first_img=$matches[1][0];
	}
    if(empty($first_img)){//
		$first_img='';
    }
    return $first_img;
}
?>