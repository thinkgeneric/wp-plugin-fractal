<?php
namespace Fractal\Admin;

use Fractal\controllers\ActionFilterController;

class FactualImportPage extends Submenu {

	protected $importer;

	public function __construct( $options, ActionFilterController $loader, FactualImporter $importer ) {
		parent::__construct( $options, $loader );
		$this->importer = $importer;
	}

	public function boot()
	{
		parent::boot();
		$this->importer->boot();
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

	}

	public function render()
	{
		// todo add datatables to frontend stuff
		wp_enqueue_script( 'datatables' );
		wp_enqueue_script( 'datatables-select' );
		wp_enqueue_script( 'datatables-buttons' );
		wp_enqueue_style( 'datatables' );
		wp_enqueue_style( 'datatables-select' );
		wp_enqueue_style( 'datatables-buttons' );
		wp_enqueue_script( 'factual-importer' );

		include_once( 'views/factual-import.php' );
	}

	public function register_scripts() {
		wp_register_style( 'datatables', '//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css' );
		wp_register_style( 'datatables-select', '//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css' );
		wp_register_style( 'datatables-buttons', '//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css' );
		wp_register_script( 'datatables', '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'datatables-select', '//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js', array( 'jquery', 'datatables' ), '1.0', true );
		wp_register_script( 'datatables-buttons', '//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js', array( 'jquery', 'datatables' ), '1.0', true );
		wp_register_script( 'factual-importer', plugin_dir_url( __FILE__ ) . 'js/factual-importer.js', array('datatables-buttons'), '1.0', true );

		// localize the factual-importer.js
		wp_localize_script( 'factual-importer', 'factualAjax', array(
			'ajaxurl' => admin_url('admin-ajax.php' ),
			'factualImportNonce' => wp_create_nonce( 'import-listing' )
		) );
	}
}