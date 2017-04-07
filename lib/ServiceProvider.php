<?php
namespace lib;

abstract class ServiceProvider {
	/*
	 * The container instance.
	 *
	 * @var \Blackriver\Container
	 */
	protected $container;

	/**
	 * Create a new service provider instance
	 * @param $container
	 */
	public function __construct( $container )
	{
		$this->container = $container;
	}

	/**
	 * Register the service provider
	 * @return void
	 */
	abstract public function register();

	/**
	 * Dynamically handle missing method calls.
	 *
	 * @param $method
	 * @param $parameters
	 *
	 * @throws \Exception
	 */
	public function __call( $method, $parameters )
	{
		if( $method == 'boot' )
		{
			return;
		}

		throw new \Exception( "Call to undefined method [{$method}]");
	}
}