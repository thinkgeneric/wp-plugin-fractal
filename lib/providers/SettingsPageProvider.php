<?php

namespace lib\providers;

use lib\Admin\FactualImporter;
use lib\Admin\FactualImportPage;
use lib\ServiceProvider;

class SettingsPageProvider extends ServiceProvider{


	public function boot()
	{
//		dd("Hello from SettingsPageProvider::boot!");
	}
	/**
	 * Register the service provider
	 * @return void
	 */
	public function register( )
	{
		$this->container->singleton( 'factual_importer', function( $app ) {
			return new FactualImporter( 'factual-import', 'fetch-listings',  $app['factual'] );
		});

		$this->container->singleton( 'settings_page', function( $app ){
			return new FactualImportPage( array(
				'page_title' => 'Factual Importer',
				'menu_title' => 'Factual Importer',
				'capability' => 'manage_options',
				'menu_slug' => 'factual-importer'
			), $app['action_filter_controller'], $app['factual_importer'] );
		});
	}
}