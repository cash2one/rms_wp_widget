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
function wp_egg_rms_options(){
        $message='Update Success';
        if($_POST['update_egg_rms_option']){
		/*
                $wp_egg_rms_number = get_option("EGG_NUMBER_OF_RMS");
                $wp_egg_rms_number_new = $_POST['EGG_NUMBER_OF_RMS'];
                if ($wp_egg_rms_number != $wp_egg_rms_number_new)
                        if(!update_option("EGG_NUMBER_OF_RMS",$wp_egg_rms_number_new))
                                $message='Update Fail';
                $wp_egg_rms_date_range = get_option("EGG_DATE_RANGE_OF_RMS");
                $wp_egg_rms_date_range_new = $_POST['EGG_DATE_RANGE_OF_RMS'];
                if($wp_egg_rms_date_range != $wp_egg_rms_date_range_new)
                        if(!update_option("EGG_DATE_RANGE_OF_RMS",$wp_egg_rms_date_range_new))
                                $message='Update Fail';
								
				$wp_egg_rms_domain = get_option("EGG_DOMAIN_OF_RMS");
                $wp_egg_rms_domain_new = $_POST['EGG_DOMAIN_OF_RMS'];
                if($wp_egg_rms_domain != $wp_egg_rms_domain_new)
                        if(!update_option("EGG_DOMAIN_OF_RMS",$wp_egg_rms_domain_new))
                                $message='Update Fail';
                //update_egg_rms();
				*/
				update_post_option("EGG_NUMBER_OF_RMS");
				update_post_option("EGG_DATE_RANGE_OF_RMS");
				update_post_option("EGG_DOMAIN_OF_RMS");
				update_post_option("EGG_TARGET_DOMAIN");
				update_post_option("EGG_MAX_POST_NUMBER");
				
				
				
                echo '<div class="updated"><strong><p>'. $message . '</p></strong></div>';
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
			<select name="EGG_NUMBER_OF_RMS">
				<?php
				 $numArr=array(3,4,5,6,7);
				 $n=get_option("EGG_NUMBER_OF_RMS");
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
			<select name="EGG_DATE_RANGE_OF_RMS">
				<?php
				 $dateArr=array(7,14,30,"unlimited");
				 $n=get_option("EGG_DATE_RANGE_OF_RMS");
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
		<label for='EGG_DOMAIN_OF_RMS'>Egg RMS Domain </label>
		</th>
		<td><input id='EGG_DOMAIN_OF_RMS' type='text' name='EGG_DOMAIN_OF_RMS' value='<?php echo get_option("EGG_DOMAIN_OF_RMS");?>'/>
		</td>
		<tr>
		<tr>
		<th>
		<label for='EGG_TARGET_DOMAIN'>Target Domain </label>
		</th>
		<td><input id='EGG_TARGET_DOMAIN' type='text' name='EGG_TARGET_DOMAIN' value='<?php echo get_option("EGG_TARGET_DOMAIN");?>'/>
		</td>
		<tr>
		<tr>
		<th>
		<label for='EGG_MAX_POST_NUMBER'>Max Push Numbers Per Time</label>
		</th>
		<td><input id='EGG_MAX_POST_NUMBER' type='text' name='EGG_MAX_POST_NUMBER' value='<?php echo get_option("EGG_MAX_POST_NUMBER");?>'/>
		</td>
		<td>
		Default is 5. 
		</td>
		<tr>
		</table>
        <p class="submit">
          <input type="submit" name="update_egg_rms_option" value="Update Options" />
        </p>
      </form>
    </div>
<?php
}

function wp_egg_rms_options_admin(){
	add_options_page('EGG_RMS_OPTIONS', 'Egg Rms Options', 5,  __FILE__, 'wp_egg_rms_options');
}

add_action('admin_menu', 'wp_egg_rms_options_admin');

