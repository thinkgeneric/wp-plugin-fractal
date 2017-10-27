<?php
namespace Fractal\providers;

use Fractal\ServiceProvider;

class FactualServiceProvider extends ServiceProvider{
	public function boot(){
		
	}
	
	public function register() {

		require_once $this->container->get_path() . 'vendor' . DIRECTORY_SEPARATOR . 'factual' . DIRECTORY_SEPARATOR . 'Factual.php';

		$this->container->singleton( 'factual', function( $app ) {
			$oauth_key = 'KYutVqJqW6ZApo1RMekAiwmyHlSqzBmF6lroD0wf';
			$oauth_secret = 'W7qQyE0aijCJP2mI46povBhukzdckEEsfEC7Y04w';
			return new \Factual( $oauth_key, $oauth_secret );
		});
	}
}