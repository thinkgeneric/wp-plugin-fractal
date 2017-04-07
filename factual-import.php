<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.thinkgeneric.com
 * @since             1.0.0
 * @package           Factual_Import
 *
 * @wordpress-plugin
 * Plugin Name:       Factual Import
 * Plugin URI:        https://www.thinkgeneric.com
 * Description:       Allows admins to import data from Factual into the Listify environment.
 * Version:           1.0.0
 * Author:            Eric Eliseo
 * Author URI:        https://thinkgeneric.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       factual-import
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
use lib\Container;
use lib\providers\ActionFilterControllerProvider;
use lib\providers\FactualServiceProvider;
use lib\providers\SettingsPageProvider;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if( ! function_exists( 'dd' ) ) {
	function dd( $var, $die = true ) {
		echo "<pre>";
		var_dump( $var );
		echo "</pre>";
		if( $die )
			die();
	}
}

//todo move the CMB2 include stuff somewhere better
if ( file_exists(  __DIR__ . '/vendor/cmb2/init.php' ) ) {
	require_once  __DIR__ . '/vendor/cmb2/init.php';
} elseif ( file_exists(  __DIR__ . '/vendor/CMB2/init.php' ) ) {
	require_once  __DIR__ . '/vendor/CMB2/init.php';
}


// Begin autoloading code
spl_autoload_register( 'factual_import_autoloader' );
function factual_import_autoloader( $class_name ) {
//	dd($class_name);
	if ( false !== strpos( $class_name, 'lib' ) ) {
		$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR ;
		$class_file = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name ) . '.php';
		require_once $classes_dir . $class_file;
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function factual_import_boot() {
	$container = new Container(); // create the container
	$container['path'] = plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR;
	$container['url'] = get_template_directory_uri();
	$container['version'] = "0.5.0";

	//pseudo service provider
	//todo create a service provider class
	$service_providers = array(
		'settings_page_provider' => SettingsPageProvider::class,
		'factual_provider' => FactualServiceProvider::class,
		'action_filter_provider' => ActionFilterControllerProvider::class,
	);

	foreach( $service_providers as $service_provider => $provider_class )
	{
		$object = new $provider_class( $container );
		$object->register();
//    $object->boot();
	}
	$container->boot();
}

factual_import_boot();
