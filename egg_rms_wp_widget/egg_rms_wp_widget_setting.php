<?php
function wp_egg_rms_options(){
        $message='Update Success';
        if($_POST['update_egg_rms_option']){
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
                //update_egg_rms();
                echo '<div class="updated"><strong><p>'. $message . '</p></strong></div>';
        }
		
		
?>

    <div class="wrap">
      <form method="post" action="">
        <h2>Options for RMS</h2>
        <div>
			<label>Number of Article</label>
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
			</div>
			
			<div>
			<label>Article Date Range</label><span>
			
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
			
			</span>
		</div>
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

