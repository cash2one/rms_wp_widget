<?php
function update_post_option($name){
	$old=get_option($name);
	$new=$_POST[$name];
	if($old!=$new){
		if(!update_option($name,$new))
              return -1;
	}
	return 0;

}
function wp_upstory_options(){
        $message='Update Success';
        if($_POST['update_upstory_option']){
		/*
                $wp_upstory_number = get_option("UPSTORY_NUMBER_OF_RMS");
                $wp_upstory_number_new = $_POST['UPSTORY_NUMBER_OF_RMS'];
                if ($wp_upstory_number != $wp_upstory_number_new)
                        if(!update_option("UPSTORY_NUMBER_OF_RMS",$wp_upstory_number_new))
                                $message='Update Fail';
                $wp_upstory_date_range = get_option("UPSTORY_DATE_RANGE_OF_RMS");
                $wp_upstory_date_range_new = $_POST['UPSTORY_DATE_RANGE_OF_RMS'];
                if($wp_upstory_date_range != $wp_upstory_date_range_new)
                        if(!update_option("UPSTORY_DATE_RANGE_OF_RMS",$wp_upstory_date_range_new))
                                $message='Update Fail';
								
				$wp_upstory_domain = get_option("UPSTORY_DOMAIN");
                $wp_upstory_domain_new = $_POST['UPSTORY_DOMAIN'];
                if($wp_upstory_domain != $wp_upstory_domain_new)
                        if(!update_option("UPSTORY_DOMAIN",$wp_upstory_domain_new))
                                $message='Update Fail';
                //update_upstory();
				*/
				update_post_option("UPSTORY_NUMBER_OF_RMS");
				update_post_option("UPSTORY_DATE_RANGE_OF_RMS");
				update_post_option("UPSTORY_DOMAIN");
				update_post_option("UPSTORY_TARGET_DOMAIN");
				update_post_option("UPSTORY_MAX_POST_NUMBER");	
				update_post_option("upstory_ADVERTISER_ID");
                echo '<div class="updated"><strong><p>'. $message . '</p></strong></div>';
        }else if($_POST['reset_UPSTORY_se_max_id']){
			update_option("UPSTORY_SE_MAX_POST_ID",0);
			
			echo '<div class="updated"><strong><p>'. $message . '： Recrawl All Posts</p></strong></div>';
		
		}
		
		
?>

    <div class="wrap">
      <form method="post" action="">
        <h2>Options for RMS</h2>
        <table class='form-table'>
		<tr>
		<th>
			<label>Number of Article</label>
			</th>
			<td>
			<select name="UPSTORY_NUMBER_OF_RMS">
				<?php
				 $numArr=array(3,4,5,6,7);
				 $n=get_option("UPSTORY_NUMBER_OF_RMS");
				foreach($numArr as $d){
					$s=($d==$n)?"selected=1":"";
					echo "<option ".$s.">".$d."</option>";
				}
				?>
			</select>
			</td>
			</tr>
			
			
		<tr>
		<th>
			<label>Article Date Range</label><span>
			</th>
			<td>
			<select name="UPSTORY_DATE_RANGE_OF_RMS">
				<?php
				 $dateArr=array(7,14,30,"unlimited");
				 $n=get_option("UPSTORY_DATE_RANGE_OF_RMS");
				foreach($dateArr as $d){
					$s=($d==$n)?"selected=1":"";
					echo "<option ".$s.">".$d."</option>";
				}
				?>
			</select> days
			</td>
			<td>
			</tr>
		<tr>
		<th>
		<label for='UPSTORY_DOMAIN'>UPSTORY Server Host </label>
		</th>
		<td><input id='UPSTORY_DOMAIN' type='text' name='UPSTORY_DOMAIN' value='<?php echo get_option("UPSTORY_DOMAIN");?>'/>
		</td>
		<tr>
		<tr>
		<th>
		<label for='UPSTORY_TARGET_DOMAIN'>Target Domain </label>
		</th>
		<td><input id='UPSTORY_TARGET_DOMAIN' type='text' name='UPSTORY_TARGET_DOMAIN' value='<?php echo get_option("UPSTORY_TARGET_DOMAIN");?>'/>
		</td>
		<tr>
		<tr>
		<th>
		<label for='UPSTORY_MAX_POST_NUMBER'>Max Push Numbers Per Time</label>
		</th>
		<td><input id='UPSTORY_MAX_POST_NUMBER' type='text' name='UPSTORY_MAX_POST_NUMBER' value='<?php echo get_option("UPSTORY_MAX_POST_NUMBER");?>'/>
		</td>
		<td>
		Default is 5. 
		</td>
		<tr>
		<tr>
		<th>
		<label for='upstory_ADVERTISER_ID'>Advertiser ID</label>
		</th>
		<td><input id='upstory_ADVERTISER_ID' type='text' name='upstory_ADVERTISER_ID' value='<?php echo get_option("upstory_ADVERTISER_ID");?>'/>
		</td>
		<td>
		</td>
		<tr>
		</table>
		<p class="submit">
          <input type="submit" name="reset_UPSTORY_se_max_id" value="Recrawl All Posts" />
        </p>
        <p class="submit">
          <input type="submit" name="update_upstory_option" value="Update Options" />
        </p>
      </form>
    </div>
<?php
}

function wp_upstory_options_admin(){
	add_options_page('upstory_OPTIONS', 'UPSTORY Options', 5,  __FILE__, 'wp_upstory_options');
}

add_action('admin_menu', 'wp_upstory_options_admin');

