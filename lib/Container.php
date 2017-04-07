<?php
namespace lib;

use ArrayAccess;
use Closure;
use ReflectionClass;

class Container implements ArrayAccess {

	protected $contents;
	protected $bindings;
	protected $instances;

	public function __construct()
	{
		$this->contents = array();
		$this->bindings = array();
		$this->instances = array();
	}

	/**
	 * Bind a single instance to the container
	 *
	 * @param $abstract
	 * @param null $concrete
	 */
	public function singleton( $abstract, $concrete = null )
	{
		$this->bind( $abstract, $concrete, true );
	}
	
	public function get_path()
	{
		return $this->contents['path'];
	}

	/**
	 * Bind instance to container
	 *
	 * @param $abstract
	 * @param null $concrete
	 * @param bool $shared
	 */
	public function bind( $abstract, $concrete = null, $shared = false )
	{
		$this->bindings[$abstract] = compact('concrete', 'shared');
	}

	// todo - need to implement the share method for objects that should be shared
	public function share( Closure $closure )
	{
		return function ($container) use ($closure) {
			static $object;

			if( is_null( $object ))
			{
				$object = $closure($container);
			}

			return $object;
		};
	}


	protected function getConcrete($abstract)
	{
		return $this->bindings[$abstract]['concrete'];
	}

	/**
	 * test
	 */
	public function boot()
	{
		foreach ( $this->bindings as $abstract => $content )
		{
			if( !$content instanceof Closure )
			{
				$content = $this->getConcrete($abstract); // so now we have a closure
			}

			$this->instances[$abstract] = $content; // add the closure to the instances array
			$this->make( $abstract, $content ); //send the closure to Container::make();

		}
	}

	// $content here is a closure
	public function make( $abstract, $content )
	{
		if( is_callable( $content) )
		{
			// we know that the $content is a closure, so let's call it and get the instance
			$content = call_user_func( $this->instances[ $abstract ], $this ); // this is the key
		}

		if( is_object( $content) )
		{
			$reflection = new ReflectionClass( $content ); // should return ["name"] => "Blackriver\Admin\SettingsPage"
			if( $reflection->hasMethod( 'boot' ) ) {
				$content->boot();
			}
		}
	}

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset
	 * An offset to check for.
	 * @return boolean true on success or false on failure.
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset )
	{
		return isset( $this->contents[ $offset ] );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset )
	{
		//todo need to set this up to use the shared() method
		// todo - make a get closure function that takes the $this->contents[$offset] and returns $this->contents[$offset]['concrete']

		if( is_callable ( $this->bindings[ $offset ]['concrete'] ) ) {
			return call_user_func( $this->bindings[ $offset ]['concrete'], $this );
		}

		//Check if the $offset is a bindings, if so get the binding
		if( is_callable( $this->contents[ $offset ] ) )
		{
			return call_user_func( $this->contents[ $offset ], $this );
		}
		// Otherwise, get the content $offset
		return isset( $this->contents[ $offset ] ) ? $this->contents[ $offset ] : null;
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value )
	{
		$this->contents[ $offset ] = $value;
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset
	 * The offset to unset.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset )
	{
		unset( $this->contents[ $offset ] );
	}


}