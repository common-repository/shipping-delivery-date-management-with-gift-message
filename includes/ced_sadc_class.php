<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( ! class_exists( 'CED_SADC_DIR' ) )
{
	class CED_SADC_DIR
	{
		/**
		  * This is a class constructor where all actions and filters are defined'.
		  * @name __construct()
		  * @author CedCommerce<plugins@cedcommerce.com>
		  * @link http://cedcommerce.com/
		  */
		public function __construct()
		{
			add_action( 'wp_enqueue_scripts', array($this,'ced_sadc_show_hide_js'));
			add_action( 'admin_menu', array($this, 'ced_sadc_shipping_and_delivery_configuration_setting'));
			add_filter( 'woocommerce_get_item_data', array ($this, 'ced_sadc_add_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array($this, 'ced_sadc_add_cart_item_data'), 10, 2);

			add_filter( 'woocommerce_get_cart_item_from_session', array ($this,'ced_sadc_get_cart_session_data'), 10, 2 );
			add_action( 'woocommerce_add_order_item_meta', array ( $this, 'ced_sadc_order_item_meta' ), 10, 2 );
			add_action( 'wp_ajax_sadc_fetch_state', array ( $this, 'ced_sadc_fetch_state_callback' ));
			add_action( 'wp_ajax_nopriv_sadc_fetch_state', array ( $this, 'ced_sadc_fetch_state_callback' ));
			add_action('admin_enqueue_scripts', array ( $this, 'ced_sadc_custom_style_script'));
			add_action( 'woocommerce_before_add_to_cart_form', array($this, 'ced_sadc_check_product_options'));
			add_filter( 'woocommerce_display_item_meta', array ( $this, 'ced_sadc_woocommerce_display_item_meta'), 10, 3 );
			add_action("wp_ajax_ced_sadc_send_mail",array($this,"ced_sadc_send_mail"));
		}
		

		function ced_sadc_send_mail()
		{
			if(isset($_POST["flag"]) && $_POST["flag"]==true && !empty($_POST["emailid"]))
			{
				$to = "support@cedcommerce.com";
				$subject = "Wordpress Org Know More";
				$message = 'This user of our woocommerce extension "Shipping Delivery Date Management with gift message" wants to know more about marketplace extensions.<br>';
				$message .= 'Email of user : '.$_POST["emailid"];
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$flag = wp_mail( $to, $subject, $message);	
				if($flag == 1)
				{
					echo json_encode(array('status'=>true,'msg'=>__('Soon you will receive the more details of this extension on the given mail.',"shipping-delivery-date-management-with-gift-message")));
				}
				else
				{
					echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again.',"shipping-delivery-date-management-with-gift-message")));
				}
			}
			else
			{
				echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again.',"shipping-delivery-date-management-with-gift-message")));
			}
			wp_die();
		}
		/** 
          *This function is used to show adress fields on order detail page'.
		  * @name ced_sadc_woocommerce_display_item_meta()
		  * @author CedCommerce<plugins@cedcommerce.com>
		  * @link http://www.cedcommerce.com/ 
    */

		function ced_sadc_woocommerce_display_item_meta($html, $item, $args){
			$strings = array();
			$html    = '';
			$args    = wp_parse_args( $args, array(
					'before'    => '<ul class="wc-item-meta"><li>',
					'after'		=> '</li></ul>',
					'separator'	=> '</li><li>',
					'echo'		=> true,
					'autop'		=> false,
			) );
			
			
			
			foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
				$value = $meta->display_value;
				$strings[] = '<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . $value;
			}
			
			if ( $strings ) {
				$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
			}
			
			if ( $args['echo'] ) {
				echo $html;
			} else {
				return $html;
			}
		}



		
		/**
		 * This function is used for checking product categories to display form.
		 * @name ced_sadc_check_product_options()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */

		function ced_sadc_check_product_options(){
			
			global $product;
            $current_product_terms = get_the_terms( $post->ID, 'product_cat' );
			$sadc_setting = get_option(CED_SADC_PREFIX.'settings', false);
			$sadc_setting = json_decode($sadc_setting, true);

			if(is_array($current_product_terms)){

				if(isset($sadc_setting['ced_sadc_product_categories'])){
					$selected_categories = $sadc_setting['ced_sadc_product_categories'];
					foreach ($current_product_terms as $key => $value) {
						if(in_array($value->slug, $selected_categories) || in_array('select_all_cat', $selected_categories) ){
	                       add_action( 'woocommerce_before_add_to_cart_button', array($this,'ced_sadc_single_product_summary_textarea'));
						}
					}
				}
			
			}elseif(!is_array($current_product_terms) && !$current_product_terms){
				if(isset($sadc_setting['ced_sadc_product_categories'])){
					$selected_categories = $sadc_setting['ced_sadc_product_categories'];
					
					if(in_array('select_all_cat', $selected_categories)){
	                       add_action( 'woocommerce_before_add_to_cart_button', array($this,'ced_sadc_single_product_summary_textarea'));
					}
				}
			}
		}



		/**
		 * Include js and css
		 * @name ced_sadc_custom_style_script()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		
		function ced_sadc_custom_style_script(){
			
			
			
			$locale  = localeconv();
			$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
			
			$params = array(
				/* translators: %s: decimal */
				'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
				/* translators: %s: price decimal separator */
				'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
				'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
				'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
				'decimal_point'                     => $decimal,
				'mon_decimal_point'                 => wc_get_price_decimal_separator(),
				'strings' => array(
					'import_products' => __( 'Import', 'woocommerce' ),
					'export_products' => __( 'Export', 'woocommerce' ),
				),
				'urls' => array(
					'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
					'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
				),
			);

			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
			wp_enqueue_script( 'woocommerce_admin' );
			
			
			wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_register_style( 'ced_sadc_admin_styles', CED_SADC_DIR_URL . '/assets/css/ced_sadc_admin.css', array(), WC_VERSION );
			wp_enqueue_style( 'ced_sadc_admin_styles' );
			wp_enqueue_style( 'ced_sadc_admin_styles',   CED_SADC_DIR_URL . '/assets/css/ced_sadc_admin.css','2.1.1', 'all' );
			wp_enqueue_style( 'ced-boot-css', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '2.0.0', 'all' );
			wp_enqueue_style( 'ced-sadc-select2-css', WC()->plugin_url() .'/assets/css/select2.css', array(), WC_VERSION  );
			wp_enqueue_style( 'ced-sadc-select2-css' );

			wp_enqueue_script( 'ced-sadc-select2-js', WC()->plugin_url() . '/assets/js/select2/select2.min.js' , array( 'jquery' ), WC_VERSION, true );
			wp_enqueue_script( 'ced-sadc-select2-js' );

			wp_enqueue_script( 'ced_sadc_custom_script',CED_SADC_DIR_URL .'assets/js/sads_admin.js', array( 'jquery', 'ced-sadc-select2-js' ), WC_VERSION, true );
			wp_localize_script("ced_sadc_custom_script","ajax_url",admin_url('admin-ajax.php'));
		}

		/**
		 * Include js and css
		 * @name ced_sadc_show_hide_js()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_sadc_show_hide_js()
		{
			if(!is_product() && !is_shop())
			{
				wp_enqueue_script( 'ced_sadc_show_hide_js', CED_SADC_DIR_URL.'/assets/js/ced_sadc_show_hide.js', array('jquery'), "1.2.0", false);
			}
			
			wp_enqueue_style( 'ced_sadc_css', CED_SADC_DIR_URL.'/assets/css/ced_sadc.css', false );
		}
		
		
		/**
		 * This function is for fetching states of country'.
		 * @name ced_sadc_fetch_state_callback()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_sadc_fetch_state_callback()
		{
			$response = array();
			$country_id = sanitize_text_field($_POST['country']);
			$path = WP_PLUGIN_DIR.'/woocommerce';
			if (file_exists($path.'/i18n/states/'.$country_id.'.php') )
			{
				include($path.'/i18n/states/'.$country_id.'.php');
				$shipping_state = $states[$country_id];
		
				$response['state'] = $shipping_state;
			}
			echo json_encode($response);
			die;
		}

		/**
		  * This function is used to show fields on Product detail page'.
		  * @name ced_sadc_single_product_summary_textarea()
		  * @author CedCommerce<plugins@cedcommerce.com>
		  * @link http://cedcommerce.com/
		  */
		function ced_sadc_single_product_summary_textarea()
		{

			$sadc_setting = get_option(CED_SADC_PREFIX.'settings', false);
			$sadc_setting = json_decode($sadc_setting, true);
				
			if(isset($sadc_setting['ced_sadc_enabled']))
			{
				if($sadc_setting['ced_sadc_enabled'] == 1)
				{
					$login_required = 'no';
					if($login_required != 'no')
					{
						if(!is_user_logged_in())
						{
							return false;
						}
					}
					
					$disable_date = '';
					
					if(isset($sadc_setting['ced_sadc_supply_order']))
					{
						$selected_days = $sadc_setting['ced_sadc_supply_order'];
					}
					
					$selected_day = [];
					if(isset($selected_days))
					{
						foreach($selected_days as $val)
						{
							$selected_day[$val] = $val;
						}
					}
					wp_enqueue_style( 'wp-jquery-ui-dialog' );
					
					wp_enqueue_style( 'jquery_timepicker_css',CED_SADC_DIR_URL .'assets/css/jquery.ui.timepicker.css');
					wp_enqueue_script( 'jquery_timepicker_script',CED_SADC_DIR_URL .'assets/js/jquery.ui.timepicker.js');
					wp_enqueue_script('jquery-ui-datepicker');
					wp_register_script( 'sads_handle',CED_SADC_DIR_URL .'assets/js/sads_setting.js',array('jquery'),'',true);
					$sadc_setting = get_option(CED_SADC_PREFIX.'settings', false);
					$sadc_setting = json_decode($sadc_setting, true);
					$translation_array = array(
							'sadc_settings' => $sadc_setting,
							'woo_curncy'	=> get_woocommerce_currency_symbol(),
							'ajaxurl' 		=> admin_url('admin-ajax.php'),
							'alerttext'		=>__('Recipient Address Saved','shipping-delivery-date-management-with-gift-message'),
					);
					wp_localize_script( 'sads_handle', 'sads_obj', $translation_array );
						
					// Enqueued script with localized data.
					wp_enqueue_script( 'sads_handle');
					?>
					<p class="ced_sadc_add_cost price ced_sadc_form_fields" style="display:none;"><b><?php _e('Additional Cost :','shipping-delivery-date-management-with-gift-message');?></b><span class="ced_sadc_add_cost_amount amount"></span><input type="hidden" name="ced_sadc_hide_cost_amount" class="ced_sadc_hide_cost_amount"></p>
					<?php 
					if(isset($sadc_setting['ced_sadc_enabled_dd_selection']))
					{
						if($sadc_setting['ced_sadc_enabled_dd_selection'])
						{?>
					    <div class="ced_sadc_form_div">
						<p class="ced_sadc_pick_dlvry_date ced_sadc_form_fields"><label> <?php _e('Pick a Delivery Date','shipping-delivery-date-management-with-gift-message');?></label></p>
						<input type="text" readonly class="sddmwgm_datepicker" name="ced_sadc_delivery_date" id="sddmwgm_datepicker">
						</div>
						<?php 
						}
					}
					if(isset($sadc_setting['ced_sadc_enabled_dd_selection'])){
					if(isset($sadc_setting['ced_sadc_enabled_dt_selection']))
					{
						if($sadc_setting['ced_sadc_enabled_dt_selection'])
						{?>
					    <div class="ced_sadc_form_div">
						<p class="ced_sadc_set_dlvry_time ced_sadc_form_fields"><label> <?php _e('Set Delivery Time Range','shipping-delivery-date-management-with-gift-message');?></label></p>
						<span>
							<p>
								<label for="ced_sadc_delivery_time_from">From:</label>
								<input type="text" id="ced_sadc_delivery_time_from" class="timepicker" name="ced_sadc_delivery_time_from" value="" >
							</p>
							<p>
								<label for="ced_sadc_delivery_time_to">To:</label>
								<input type="text" id="ced_sadc_delivery_time_to" class="timepicker" name="ced_sadc_delivery_time_to" value="" >
							</p>
						</span>
						</div>
						<?php 
						}
					}
					}		
					if(isset($sadc_setting['ced_sadc_enabled_gm_function']))
					{
						if($sadc_setting['ced_sadc_enabled_gm_function'])
						{
							$padtop = "0px";$padright = "0px";$padbottom = "0px";$padleft = "0px";
							if(isset($sadc_setting['ced_sadc_padtop'])&&!empty($sadc_setting['ced_sadc_padtop'])){
								$padtop = $sadc_setting['ced_sadc_padtop'];
							}
							if(isset($sadc_setting['ced_sadc_padright'])&&!empty($sadc_setting['ced_sadc_padright'])){
								$padright = $sadc_setting['ced_sadc_padright'];
							}
							if(isset($sadc_setting['ced_sadc_padbottom'])&&!empty($sadc_setting['ced_sadc_padbottom'])){
								$padbottom = $sadc_setting['ced_sadc_padbottom'];
							}
							if(isset($sadc_setting['ced_sadc_padleft'])&&!empty($sadc_setting['ced_sadc_padleft'])){
								$padleft = $sadc_setting['ced_sadc_padleft'];
							}
							if(isset($sadc_setting['ced_sadc_gm_length'])&&!empty($sadc_setting['ced_sadc_gm_length'])){
								$gift_msg_length = $sadc_setting['ced_sadc_gm_length'];
							}


							?>
						<div class="ced_sadc_form_div">	
						<p class="ced_sadc_enter_gift_msg ced_sadc_form_fields"><label><?php _e('Enter Gift a Message','shipping-delivery-date-management-with-gift-message');?></label></p>
						<div style="padding: <?php echo $padtop;?> <?php echo $padright;?> <?php echo $padbottom;?> <?php echo $padleft;?>"><textarea placeholder="enter message upto <?php echo $gift_msg_length; ?> characters...." class="sddmwgm_gift_msg" name="ced_sadc_gift_msg" maxlength="<?php echo $gift_msg_length; ?>"></textarea></div>
						</div>
						<?php 
						}
					}		
					if(isset($sadc_setting['ced_sadc_enabled_ra_selection']))
					{
						if($sadc_setting['ced_sadc_enabled_ra_selection'])
						{
					global $woocommerce;
					$countries = $woocommerce->countries->countries;
					?>
						

					<div id="ced_sadc_dialog" title="Basic dialog" style="display:none;">
					<div class="close-btn">
						<a href="#" class="cancel">&times;</a>	
					</div>
						<div class="ced-billing-fields">
							<p class="form-row form-row">
								<label><?php _e('First Name','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="text" id="ced_sadc_first_name_field" name="ced_sadc_first_name_field" class="input-text ced_sadc_required">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Last Name','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="text"id="ced_sadc_last_name_field" name="ced_sadc_last_name_field" class="input-text ced_sadc_required">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Company Name','shipping-delivery-date-management-with-gift-message');?></label>
								<input type="text" id="ced_sadc_company_field" name="ced_sadc_company_field" class="input-text ">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Email Address','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="email" id="ced_sadc_email_field" name="ced_sadc_email_field" class="input-text ced_sadc_required">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Phone','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="tel" id="ced_sadc_phone_field" name="ced_sadc_phone_field" class="input-text ced_sadc_required">
							</p>
							<p class="form-row form-row form-row-wide">
								<label><?php _e('Country','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<select name="ced_sadc_country_field"  id="ced_sadc_country_field">
									<option value=""><?php _e('Select Country','shipping-delivery-date-management-with-gift-message')?></option>
									<?php 
									foreach($countries as $key=>$country)
									{
									?>
									<option value="<?php echo $key?>" rel="<?php echo $country?>"><?php echo $country?></option>
									<?php 
									}
									?>
								</select>
							</p>
							<p>
								<label><?php _e('State / County','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<span id="ced_sadc_country_select">
									<input type="text" id="ced_sadc_state_field" name="ced_sadc_state_field" class="input-text ced_sadc_required">
								</span>
							</p>	
							<p class="form-row form-row">
								<label><?php _e('Address1','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="text" id="ced_sadc_address_1_field" name="ced_sadc_address_1_field" class="input-text ced_sadc_required">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Address2','shipping-delivery-date-management-with-gift-message');?></label>
								<input type="text" id="ced_sadc_address_2_field" name="ced_sadc_address_2_field" class="input-text">
							</p>
							<p class="form-row form-row">
								<label><?php _e('Town / City','shipping-delivery-date-management-with-gift-message');?> <abbr>*</abbr></label>
								<input type="text" id="ced_sadc_city_field" name="ced_sadc_city_field" class="input-text ced_sadc_required">
							</p>
							<div class="clear"></div>
							<p class="form-row form-row form-row-wide address-field validate-required" data-o_class="form-row form-row form-row-wide address-field validate-required"><input type="button" value="SAVE" class="input-text" id="ced_sadc_save" name="ced_sadc_save"></p>
						</div>
					 </div>
						
						<div class="ced_sadc_form_div">
						<label><?php _e('Recipient Address','shipping-delivery-date-management-with-gift-message');?></label>
						<p id="ced_sadc_btnshow"><?php _e('Click Here','shipping-delivery-date-management-with-gift-message');?></p>
						<input type="hidden" name="ced_sadc_rec_add" id="ced_sadc_rec_add" value="">
						</div>
						
						<?php 
						}
					}		
					?>
					<p></p>
				<?php 
				}
			}
		}
		/**
		 * This function is used to add setting menu in WooCommerce section'.
		 * @name ced_sadc_shipping_and_delivery_configuration_setting()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
				
		function ced_sadc_shipping_and_delivery_configuration_setting()
		{
			add_submenu_page( 'woocommerce', 'Shipping and Delivery Configuration', 'Shipping and Delivery Configuration', 'manage_options', 'shipping-and-delivery-configuration', array($this,'ced_sadc_shipping_and_delivery_configuration') );
		}	

		/**
		 * This function is used to add setting page in WooCommerce section.
		 * @name sadc_shipping_and_delivery_configuration()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		 function ced_sadc_shipping_and_delivery_configuration()
		 {
			include_once CED_SADC_DIR.'/admin/settings.php';
		 } 
		
		/**
		 * This function is used to add field with product.
		 * @name ced_sadc_add_item_meta()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_sadc_add_item_meta($item_meta, $existing_item_meta)
		{
			if ($existing_item_meta ['product_meta']['meta_data']) {
				foreach ( $existing_item_meta ['product_meta'] ['meta_data'] as $key => $val ) {
		
					if($key == 'ced_sadc_delivery_date')
					{
						$item_meta [] = array (
								'name' => __('Delivery Date','shipping-delivery-date-management-with-gift-message'),
								'value' => stripslashes( $val ),
						);
					}
					if($key == 'ced_sadc_delivery_time_from')
					{
						$item_meta [] = array (
								'name' => __('Delivery Time From','shipping-delivery-date-management-with-gift-message'),
								'value' => stripslashes( $val ),
						);
					}
					if($key == 'ced_sadc_delivery_time_to')
					{
						$item_meta [] = array (
								'name' => __('Delivery Time To','shipping-delivery-date-management-with-gift-message'),
								'value' => stripslashes( $val ),
						);
					}

						
					if($key == 'ced_sadc_gift_msg')
					{
						$item_meta [] = array (
								'name' => __('Gift Message','shipping-delivery-date-management-with-gift-message'),
								'value' => stripslashes( $val ),
						);
					}
						
					if($key == 'ced_sadc_rec_add')
					{
						$item_meta [] = array (
								'name' => __('Recipient Address','shipping-delivery-date-management-with-gift-message'),
								'value' => stripslashes( $val ),
						);
					}
				}
			}
			return $item_meta;
		}
		
		
		/**
		 * This function is used add setting field data to cart.
		 * @name ced_sadc_add_cart_item_data()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_sadc_add_cart_item_data($the_cart_data, $product_id)
		{
						
			
				
					unset($_POST['quantity']);
					unset($_POST['add-to-cart']);
					if(isset($_POST['ced_sadc_rec_add']))
					{
					$address = stripslashes($_POST['ced_sadc_rec_add']);
					$address = json_decode($address, true);
					}
					if(isset($address))
					{
						$address['country'] = WC()->countries->countries[$address['country']];
						$address_html = '<a class="ced_product_show_click">Click Here</a><div class="ced_product_show">';
						$address_html .= "<p class='ced_sadc_para'><b>Name :</b>".$address['firstname']." ".$address['lastname']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>Company :</b>".$address['companyname']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>Email :</b>".$address['email']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>Phone :</b>".$address['phone']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>Country :</b>".$address['country']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>State :</b>".$address['state']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>City :</b>".$address['city']."</p>";
						$address_html .= "<p class='ced_sadc_para'><b>Address :</b>".$address['address1']." ".$address['address2']."</p>";
						$address_html .= '</div>';
					}		
					
					if(empty($_POST['ced_sadc_delivery_date']))
					{
						unset($_POST['ced_sadc_delivery_date']);
						unset($_POST['ced_sadc_hide_cost_amount']);
						// unset($_POST['ced_sadc_rec_add']);
					}
					if(empty($_POST['ced_sadc_gift_msg']))
						unset($_POST['ced_sadc_gift_msg']);
					if(empty($_POST['ced_sadc_rec_add']))
					{
						unset($_POST['ced_sadc_rec_add']);
					}
					else
					{
						$_POST['ced_sadc_rec_add'] = $address_html;
					}	
					$the_cart_data ['product_meta'] = array('meta_data' => $_POST);
				
			
			return $the_cart_data;
		}
		/**
		 * This function is used get cart item at cart and checkout page.
		 * @name ced_sadc_get_cart_session_data()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_sadc_get_cart_session_data($cart_items, $values)
		{
			
			
			
				
					
					if($cart_items == '')
						return;
				
					if (isset ( $values ['product_meta'] )) :
						$cart_items ['product_meta'] = $values ['product_meta'];
						if(isset($values ['product_meta']['meta_data'])):
							if(isset($values ['product_meta']['meta_data']['ced_sadc_hide_cost_amount'])):
								$add_price = $values ['product_meta']['meta_data']['ced_sadc_hide_cost_amount'];
								if(WC()->version<'3.0.0')
     								{

								$cart_item_price = $cart_items['data']->price;
								$cart_items['data']->price = $cart_item_price + $add_price;
							}else{

								$cart_item_price = $cart_items['data']->get_price();
								$cart_items['data']->set_price( $cart_item_price + $add_price ) ;
							}
							endif;
						endif;
					endif;
				
			
				
			return $cart_items;
		}
		/**
		 * This function is used to save field with order.
		 * @name ced_sadc_order_item_meta()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_sadc_order_item_meta($item_id, $cart_item) 
		{
			
			
			if (isset ( $cart_item ['product_meta'] )) 
					{
						foreach ( $cart_item ['product_meta'] ['meta_data'] as $key => $val ) 
						{
							$order_val = stripslashes( $val );
							if($val)
								// print_r($key);

							{
								if($key == 'ced_sadc_delivery_date')
								{
									// die("---/---");
									wc_add_order_item_meta ( $item_id, 'Delivery Date', $order_val );
								}

								if($key == 'ced_sadc_delivery_time_from')
								{
									wc_add_order_item_meta ( $item_id, 'Delivery Time From', $order_val );
								}

								if($key == 'ced_sadc_delivery_time_to')
								{
									wc_add_order_item_meta ( $item_id, 'Delivery Time To', $order_val );
								}
								
								if($key == 'ced_sadc_gift_msg')
								{
									wc_add_order_item_meta ( $item_id, 'Gift Message', $order_val );
								}
								
								if($key == 'ced_sadc_rec_add')
								{
									wc_add_order_item_meta ( $item_id, 'Recipient Address', $order_val );
								}
							}
						}
					}
		} 
	}
	new CED_SADC_DIR();
}		
?>