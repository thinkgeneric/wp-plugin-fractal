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
use Fractal\Container;
use Fractal\providers\ActionFilterControllerProvider;
use Fractal\providers\FactualServiceProvider;
use Fractal\providers\SettingsPageProvider;

if ( ! defined( 'WPINC' ) ) {
	die;
}

$factual_error = function( $message, $subtitle = '', $title = '' ) {
	$title = $title ?: "TG Error";
	$footer = "<a href='https://github.com/thinkgeneric/wp-plugin-fractal'>Fractal Documentation</a>";
	$message = "<h1>$title</br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
	wp_die( $message, $title );
};

/**
 * Make sure that we have Composer's dependencies installed. We will also use Composer
 * as the autoloader for the whole theme. This means that for classes and packages to be used
 * they must be fully namespaces, using PSR-4 standards.
 */
$composer = __DIR__ . '/vendor/autoload.php';
if ( ! file_exists( $composer ) ) {
	$factual_error(
		"In order to use the Fractal plugin, please run <code>composer install</code> or <code>script/setup</code> in your terminal.",
		"Composer Autoloader not found."
	);
}
require_once( $composer );

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
	}
	$container->boot();
}

factual_import_boot();
