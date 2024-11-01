<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_GET["ced_sadc_close"]) && $_GET["ced_sadc_close"]==true)
{
	unset($_GET["ced_sadc_close"]);
	if(!session_id())
		session_start();
	$_SESSION["ced_sadc_hide_email"]=true;
} 
if(isset($_POST['ced_sadc_save']))
{
	$setting = array();
	$settings = $_POST;
	foreach ($settings as $key=>$value)
	{
		if(is_array($value))
		{
			foreach ($value as $k=>$val)
			{
				$value[$k]=sanitize_text_field($val);
				$setting[$key]=$value;
			}
		}
		else 
		{
			$setting[$key]=sanitize_text_field($value);
		}
		
	}
	
	$sadc_setting = json_encode($setting);
	update_option(CED_SADC_PREFIX.'settings', $sadc_setting);
}

$setting = get_option(CED_SADC_PREFIX.'settings', false);
$setting = json_decode($setting, true);

if(isset($setting['ced_sadc_date_format']))
{
	$selected_date = $setting['ced_sadc_date_format'];
}
if(isset($setting['ced_sadc_supply_order']))
{
	$selected_days = $setting['ced_sadc_supply_order'];
	$selected_day = [];
	if(isset($selected_days))
	{
		foreach($selected_days as $val)
		{
			$selected_day[$val] = $val;
		}
	}
}
if(isset($setting['ced_sadc_product_categories']))
{
	$selected_categories = $setting['ced_sadc_product_categories'];
	$selected_category = [];
	if(isset($selected_categories))
	{
		foreach($selected_categories as $val)
		{
			$selected_category[$val] = $val;	
		}
	}
}

$args = array( 'taxonomy' => 'product_cat' );
$terms = get_terms('product_cat', $args);
?>

<div class="ced_sadc_setting_wrapper">
	<div class="ced_sadc_setting_section">
<form enctype="multipart/form-data" action="" id="mainform" class="ced_sadc_settings_form" method="post">
	<h3><?php _e('Shipping and Delivery Configuration','shipping-delivery-date-management-with-gift-message');?></h3>
	<hr/>
	<table class="form-table ced_sadc_settings_table">
		<tbody>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_enabled"><?php _e('Enable','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<label for="ced_sadc_enabled">
							<input type="checkbox" <?php if(isset($setting['ced_sadc_enabled'])){if($setting['ced_sadc_enabled'] == 1){?>checked="checked"<?php }}?> value="1" style="" id="ced_sadc_enabled" name="ced_sadc_enabled"><?php _e('Enable Shipping and Delivery','shipping-delivery-date-management-with-gift-message');?></label><br>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_product_categories"><?php _e('Select product categories','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<label for="ced_sadc_product_categories">
							<select id="ced_sadc_product_categories" name="ced_sadc_product_categories[]" multiple>
								<option value="select_all_cat" <?php if(in_array('select_all_cat', $selected_categories)){echo "selected";}elseif (!isset($setting['ced_sadc_product_categories'])) {
									echo "selected";
								} ?>><?php _e('Select All','shipping-delivery-date-management-with-gift-message'); ?></option>
								<?php 
								if ( !empty( $terms ) and is_array( $terms ) ) {
									foreach ( $terms as $key => $term ) {
										if ( empty( $term ) ) {
											continue;
										}

										$selected = '';
										if ( !empty( $selected_categories ) ) {
											if ( in_array( $term->slug, $selected_categories ) ) {
												$selected = 'selected';
											}
										}

										echo '<option value="'. esc_attr( $term->slug ) .'" '. $selected .'>'. $term->name .'</option>';
									}
								}
								?>
							</select>
						</label>
                        <label><div class="error ced_sadc_product_categories_error" style="display: none;">please select atleast one option</div></label>
						<br>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_date_format"><?php _e('Date format','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<select name="ced_sadc_date_format">
							<option <?php if(isset($selected_date)){ if($selected_date == "dd/mm/yy"){?>selected="selected"<?php }}?> value="dd/mm/yy">dd/mm/yy</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "yy/mm/dd"){?>selected="selected"<?php }}?> value="yy/mm/dd">yy/mm/dd</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "mm/dd/yy"){?>selected="selected"<?php }}?> value="mm/dd/yy">mm/dd/yy</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "d M, yy"){?>selected="selected"<?php }}?> value="d M, yy">d M, yy</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "DD, d MM, yy"){?>selected="selected"<?php }}?> value="DD, d MM, yy">DD, d MM, yy</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "yy-mm-dd"){?>selected="selected"<?php }}?> value="yy-mm-dd">yy-mm-dd</option>
							<option <?php if(isset($selected_date)){ if($selected_date == "mm-dd-yy"){?>selected="selected"<?php }}?> value="mm-dd-yy">mm-dd-yy</option>
						</select>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_supply_order"><?php _e('Day you can not supply order','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<select id="ced_sadc_day_selection" multiple name="ced_sadc_supply_order[]">
						    <option <?php if(isset($selected_day['7'])){ if($selected_day[7] == 7){?>selected="selected"<?php }}?> value="7"><?php _e('Supply All Day','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['0'])){ if($selected_day[0] == 0){?>selected="selected"<?php }}?> value="0"><?php _e('Sunday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['1'])){ if($selected_day[1] == 1){?>selected="selected"<?php }}?> value="1"><?php _e('Monday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['2'])){ if($selected_day[2] == 2){?>selected="selected"<?php }}?> value="2"><?php _e('Tuesday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['3'])){ if($selected_day[3] == 3){?>selected="selected"<?php }}?> value="3"><?php _e('Wednesday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['4'])){ if($selected_day[4] == 4){?>selected="selected"<?php }}?> value="4"><?php _e('Thursday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['5'])){ if($selected_day[5] == 5){?>selected="selected"<?php }}?> value="5"><?php _e('Friday','shipping-delivery-date-management-with-gift-message');?></option>
							<option <?php if(isset($selected_day['6'])){ if($selected_day[6] == 6){?>selected="selected"<?php }}?> value="6"><?php _e('Saturday','shipping-delivery-date-management-with-gift-message');?></option>
						</select>
					</fieldset>
				</td>
			</tr>
			
			<tr valign="top">
				<th class="titledesc" scope="row" colspan="2">
					<label class="ced_additional_cost"><?php _e('Additional cost for days','shipping-delivery-date-management-with-gift-message');?></label>
					<hr/ class="ced_hr_additional_cost">
					<table class="ced_sadc_add_day_cost">
						<tr>
							<td>
								<span>
									<label for="ced_Sunday"><?php _e('Sunday','shipping-delivery-date-management-with-gift-message');?></label>	
								</span>
								<span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_sunday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_sunday'])){echo $setting['ced_sadc_sunday'];}?>">
									</fieldset>
								</span>	
							</td>
							<td class="titledesc" scope="row">
								<span>
									<label for="ced_sadc_no_of_days"><?php _e('Monday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
								<span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_monday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_monday'])){echo $setting['ced_sadc_monday'];}?>">
									</fieldset>
								</span>	
							</td>
							<td class="titledesc" scope="row">
								<span>
									<label for="ced_sadc_no_of_days"><?php _e('Tuesday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
								<span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_tuesday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_tuesday'])){echo $setting['ced_sadc_tuesday'];}?>">
									</fieldset>
								</span>
							</td>
							<td class="titledesc" scope="row">
						        <span>
									<label for="ced_sadc_no_of_days"><?php _e('Wednesday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
						        <span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_wednesday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_wednesday'])){echo $setting['ced_sadc_wednesday'];}?>">
									</fieldset>
								</span>
							</td>
						</tr>
						<tr valign="top">
						    <td class="titledesc" scope="row">
							    <span>
									<label for="ced_sadc_no_of_days"><?php _e('Thrusday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
								<span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_thrusday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_thrusday'])){echo $setting['ced_sadc_thrusday'];}?>">
									</fieldset>
								</span>
							</td>
							<td class="titledesc" scope="row">
								<span>
									<label for="ced_sadc_no_of_days"><?php _e('Friday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
							    <span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_friday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_friday'])){echo $setting['ced_sadc_friday'];}?>">
									</fieldset>
								</span>
							</td>
							<td class="titledesc" scope="row">
								<span>
									<label for="ced_sadc_no_of_days"><?php _e('Saturday','shipping-delivery-date-management-with-gift-message');?></label>
								</span>
								<span>
									<fieldset>
										<input type="text" min="0" name="ced_sadc_saturday" class="wc_input_price input-text regular-input" value="<?php if(isset($setting['ced_sadc_saturday'])){echo $setting['ced_sadc_saturday'];}?>">
									</fieldset>
								</span>
							</td>
						</tr>	
					</table>
					<hr/>
				</th>
			</tr>	
			
			
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_enabled_dd_selection"><?php _e('Enable Delivery Date selection','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<input type="checkbox" <?php if(isset($setting['ced_sadc_enabled_dd_selection'])){if($setting['ced_sadc_enabled_dd_selection'] == 1){?>checked="checked"<?php }}?>  value="1" style="" id="ced_sadc_enabled_dd_selection" name="ced_sadc_enabled_dd_selection" class="">
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
			    <th class="titledesc" scope="row">
					<label for="ced_sadc_enabled_dt_selection"><?php _e('Enable Delivery Time selection','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<input type="checkbox" <?php if(isset($setting['ced_sadc_enabled_dt_selection'])){if($setting['ced_sadc_enabled_dt_selection'] == 1){?>checked="checked"<?php }}?>  value="1" style="" id="ced_sadc_enabled_dt_selection" name="ced_sadc_enabled_dt_selection" class="">
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_enabled_ra_selection"><?php _e('Enable Recipient address selection','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<input type="checkbox" <?php if(isset($setting['ced_sadc_enabled_ra_selection'])){if($setting['ced_sadc_enabled_ra_selection'] == 1){?>checked="checked"<?php }}?>  value="1" style="" id="ced_sadc_enabled_ra_selection" name="ced_sadc_enabled_ra_selection" class="">
					</fieldset>
				</td>
			</tr>
			
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_enabled_gm_function"><?php _e('Enable Gift message functionalities','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<input type="checkbox" <?php if(isset($setting['ced_sadc_enabled_gm_function'])){if($setting['ced_sadc_enabled_gm_function'] == 1){?>checked="checked"<?php }}?>  value="1" style="" id="ced_sadc_enabled_gm_function" name="ced_sadc_enabled_gm_function" class="">
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="ced_sadc_gm_length"><?php _e('Set Gift message length','shipping-delivery-date-management-with-gift-message');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<input type="text" <?php if(isset($setting['ced_sadc_gm_length'])){if($setting['ced_sadc_gm_length']){?><?php }}?>  value="<?php echo $setting['ced_sadc_gm_length']; ?>" style="" id="ced_sadc_gm_length" name="ced_sadc_gm_length" class="">
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row" colspan="2">
					<label class="ced_adjust_gift_padding"><?php _e('Adjust Padding for Gift Message Box in the Product Single Page','shipping-delivery-date-management-with-gift-message');?></label>
					<hr/ class="ced_adjust_hr_gift">
					<table>
						<label style="display:none;"><?php _e('Enter the gift box padding in pixels','shipping-delivery-date-management-with-gift-message');?></label>
						<tr>
							<td>
								<label for="ced_padtop"><?php _e('Padding Top','shipping-delivery-date-management-with-gift-message');?></label>
							</td>

							<td class="forminp">
								<fieldset>
									<input type="text" name="ced_sadc_padtop" class="input-text regular-input" value="<?php if(isset($setting['ced_sadc_padtop'])){echo $setting['ced_sadc_padtop'];}?>" placeholder="px">
								</fieldset>
							</td>
							<td>
								<label for="ced_padright"><?php _e('Padding Right','shipping-delivery-date-management-with-gift-message');?></label>
							</td>					
							
						
							<td class="forminp">
								<fieldset>
									<input type="text" name="ced_sadc_padright" class="input-text regular-input" value="<?php if(isset($setting['ced_sadc_padright'])){echo $setting['ced_sadc_padright'];}?>" placeholder="px">
								</fieldset>
							</td>
						</tr>
						<tr>
							
							<td>
								<label for="ced_padbottom"><?php _e('Padding Bottom','shipping-delivery-date-management-with-gift-message');?></label>
							</td>
						
							<td class="forminp">
								<fieldset>
									<input type="text" name="ced_sadc_padbottom" class="input-text regular-input" value="<?php if(isset($setting['ced_sadc_padbottom'])){echo $setting['ced_sadc_padbottom'];}?>" placeholder="px">
								</fieldset>
							</td>
							<td>
								<label for="ced_padleft"><?php _e('Padding Left','shipping-delivery-date-management-with-gift-message');?></label>
							</td>
							<td class="forminp">
								<fieldset>
									<input type="text" name="ced_sadc_padleft" class="input-text regular-input" value="<?php if(isset($setting['ced_sadc_padleft'])){echo $setting['ced_sadc_padleft'];}?>" placeholder="px">
								</fieldset>
							</td>

						</tr>
						
					</table>
					<hr/>
				</th>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" value="Save changes" class="button-primary" name="ced_sadc_save">
	</p>
</form>
<div class="ced_pre_add">
	<h3 class="ced_pre_text">Checkout the premium version of the plugin with various advance features : <a target="_blank" href="https://cedcommerce.com/woocommerce-extensions/shipping-delivery-date-management-with-gift-message">Buy Now!</a></h3>
</div>
</div>
			<div class="ced_hpul_email_image">
					<div class="ced_hpul_email_main_content">
						<div class="ced_hpul_cross_image">
							<a class="button-primary ced_hpul_cross_image" href="?<?php echo $url_params?>&ced_hpul_close=true">x</a>
						</div>
						<a href="https://cedcommerce.com/" target="_blank"><div class="ced-recom">
							<h4>CedCommerce recommendations for you </h4>
						</div></a>

						<p></p>
						<div class=""  id="ced_hpul_loader">	
							<a target="_blank" href="https://chat.whatsapp.com/BcJ2QnysUVmB1S2wmwBSnE"><img id="ced-hpul-loading-image" src="<?php echo CED_SADC_DIR_URL.'/assets/images/market-place.jpg'?>" ></a>
						</div>
						<div class="ced_hpul_banner">
							<a target="_blank" href="https://chat.whatsapp.com/BcJ2QnysUVmB1S2wmwBSnE"><img src="<?php echo CED_SADC_DIR_URL.'/assets/images/market-place-2.jpg'?>"></a>
						</div>

						<div class="wramvp-support">
							<ul>
								<li><span class="wramvp-support__left">Contact Us :-</span><a href="mailto:support@cedcommerce.com"> support@cedcommerce.com </a>  </li>
								<li><span class="wramvp-support__right">Get expert's advice :-</span><a href="https://join.skype.com/bovbEZQAR4DC"> Join Us</a></li>
							</ul>
						</div>

					</div>
				</div>

</div>




<div class="ced_contact_menu_wrap">
<input type="checkbox" href="#" class="ced_menu_open" name="menu-open" id="menu-open" />
<label class="ced_menu_button" for="menu-open">
<img src="<?php echo esc_url( CED_SADC_DIR_URL . 'assets/images/icon.png' ); ?>" alt="" title="Click to Chat">
</label>
<a href=" https://join.skype.com/UHRP45eJN8qQ " class="ced_menu_content ced_skype" target="_blank"> <i class="fa fa-skype" aria-hidden="true"></i> </a>
<a href=" https://chat.whatsapp.com/BcJ2QnysUVmB1S2wmwBSnE " class="ced_menu_content ced_whatsapp" target="_blank"> <i class="fa fa-whatsapp" aria-hidden="true"></i> </a>
</div>
