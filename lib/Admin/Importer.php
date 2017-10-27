<?php
namespace lib\Admin;


abstract class Importer {

	protected $nonce_name;
	protected $nonce_action;

	public function __construct($nonce_action, $nonce_name) {
		$this->nonce_action = $nonce_action;
		$this->nonce_name   = $nonce_name;
	}

	public function boot() {
		add_action('wp_ajax_get_factual_data', [$this, 'get_factual_data']);
		add_action('wp_ajax_import_factual_data', [$this, 'import_factual_data']);
	}

	public function get_factual_data() {
		$status = $this->maybe_fetch();
		wp_send_json_success($status);
		wp_die();
	}

	public function import_factual_data() {
		$status = $this->maybe_import();
		wp_send_json_success($status);
		wp_die();
	}

	public function maybe_import() {
		$status = '';

		if ( ! ($this->has_valid_nonce($_POST['import-listing'], 'import-listing') && current_user_can('manage_options'))) {
			$status = 'noauth-import';
		}

		if (null !== wp_unslash($_POST['import-listing'])) {
			$status = $this->import($_POST['import-rows']);
		}

		return $status;
	}

	public function maybe_fetch() {
		$status = '';

		if ( ! ($this->has_valid_nonce($_POST[ $this->nonce_name ], $this->nonce_action) && current_user_can('manage_options'))) {
			$status = 'noauth';
		}

		if (null !== wp_unslash($_POST[ $this->nonce_name ])) {
			$status = $this->fetch_data($_POST['factual_locality'], $_POST['factual_limit'], $_POST['factual_category']);
		}

		return $status;
	}

	private function has_valid_nonce($nonce_name, $nonce_action) {
		if ( ! wp_verify_nonce($nonce_name, $nonce_action)) {
			return false;
		}

		return true;
	}
}