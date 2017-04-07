<?php 
namespace lib\Admin;

use lib\controllers\ActionFilterController;

abstract class Submenu {
	protected $page_title;
	protected $menu_title;
	protected $capability;
	protected $menu_slug;
	protected $loader;

	public function __construct( $options, ActionFilterController $loader )
	{

	    extract( $options );
		$this->page_title = $page_title;
		$this->menu_title = $menu_title;
		$this->capability = $capability;
		$this->menu_slug = $menu_slug;

		$this->loader = $loader;
	}

	public function boot()
	{
		$this->loader->add_action( 'admin_menu', $this, 'add_options_page', 100 );

	}

	public function add_options_page() {
		add_options_page(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			array( $this, 'render' )
		);
	}
}