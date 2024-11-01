<?php
/**
 * Plugin Name:  Shipping Delivery Date Management with gift message
 * Plugin URI: http://cedcommerce.com
 * Description: Shipping & Delivery Date management with gift message extension allows selection of delivery date, messaging of gift hampers, shipping address management feature. Require Woocommerce version >= 2.6.0
 * Version: 2.0.0
 * Author: CedCommerce <plugins@cedcommerce.com>
 * Author URI: http://cedcommerce.com
 * Requires at least: 3.5
 * Tested up to: 6.0.0
 * Text Domain: shipping-delivery-date-management-with-gift-message
 * Domain Path: /languages
 */
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

error_reporting(0);

define('CED_SADC_DIR', plugin_dir_path( __FILE__ ));
define('CED_SADC_DIR_URL', plugin_dir_url( __FILE__ ));
define('CED_SADC_PREFIX', 'ced_sadc_');

$activated = true;
if (function_exists('is_multisite') && is_multisite())
{
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) )
	{
		$activated = false;
	}
}
else
{
	if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
	{
		$activated = false;
	}
}
/**
 * Check if WooCommerce is active
 **/
if ($activated) 
{
	$plugin = plugin_basename(__FILE__);
	include_once CED_SADC_DIR.'includes/ced_sadc_class.php';
	
	
	add_action('plugins_loaded', 'ced_sadc_load_text_domain');
	
	/**
	 * This function is used to load language'.
	 * @name ced_wuoh_load_text_domain()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_sadc_load_text_domain()
	{
		$domain = "shipping-delivery-date-management-with-gift-message";
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, CED_SADC_DIR .'languages/'.$domain.'-' . $locale . '.mo' );
		$var=load_plugin_textdomain( 'shipping-delivery-date-management-with-gift-message', false, plugin_basename( dirname(__FILE__) ) . '/languages' );
	}
}
else
{
	/**
	 * Show error notice if WooCommerce is not activated.
	 * @name ced_sadc_plugin_error_notice()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_sadc_plugin_error_notice()
	{
		?>
		<div class="error notice is-dismissible">
		<p><?php _e( 'WooCommerce is not activated. Please install WooCommerce to use the Shipping Delivery Date Management with gift message extension !!!', 'shipping-delivery-date-management-with-gift-message' ); ?></p>
		</div>
		<?php
	}
		
	add_action( 'admin_init', 'ced_sadc_plugin_deactivate' );
		
	
	/**
	 * Deactivate extension if WooCommerce is not activated.
	 * @name ced_sadc_plugin_deactivate()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_sadc_plugin_deactivate() 
	{
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'ced_sadc_plugin_error_notice' );
	}
}
?>
