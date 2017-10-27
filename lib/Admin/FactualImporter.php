<?php
namespace lib\Admin;

use \Factual;

class FactualImporter extends Importer {

	protected $factual_client;

	public function __construct($nonce_action, $nonce_name, \Factual $factual_client) {
		parent::__construct($nonce_action, $nonce_name);
		$this->factual_client = $factual_client;
	}

	protected function import($rows) {
//		wp_die();
		foreach ($rows as $row) {
			//check that post doesnt exist
			if (get_posts(['meta_key' => 'factual_id', 'meta_value' => $row['factual_id']])) {
				continue;
			}

			//insert the post
			$location = wp_insert_post([
				'post_title' => $row['name'],
				'post_type'  => 'job_listing',
				'meta_input' => [
					'factual_id'                    => $row['factual_id'],
					'_company_email'                => $row['email'],
					'_job_location'                 => $row['address'] . ', ' . $row['locality'] . ', ' . $row['region'] . ' ' . $row['[postcode'],
					'_job_hours'                    => $row['hours_display'],
					'_company_phone'                => $row['tel'],
					'_company_website'              => $row['website'],
					'geolocated'                    => 1,
					'geolocation_city'              => $row['locality'],
					'geolocation_lat'               => $row['latitude'],
					'geolocation_long'              => $row['longitude'],
					'geolocation_formatted_address' => $row['address'] . ', ' . $row['locality'] . ', ' . $row['region'] . ' ' . $row['postcode'] . ', USA',
				],
			]);

			//check if categories exist
			$categories = $this->process_factual_categories($row['category_labels']);
			foreach ($categories as $category) {
//				$slug = str_replace( ' ', '-', strtolower( $category ) );
				if ( ! term_exists($category, 'job_listing_category')) {
					wp_insert_term($category, 'job_listing_category');
				}
			}
//			echo( )
			$cat = wp_set_object_terms($location, $categories, 'job_listing_category', true);
		}
		if ($cat instanceof WP_Error) {
			return var_dump($cat);
		} else {
			return 'it worked';
		}
	}

	protected function process_factual_categories($categories) {
		$cats = [];
		foreach ($categories as $category_nest) {
			foreach ($category_nest as $category) {
				$cats[] = $category;
			}
		}

		return $cats;
	}

	protected function fetch_data($locality, $limit, $category) {
//		$limit = $_POST['factual_limit'];

		// need to process results into datatable first
		/*
		 * We want the following attributes
		 *
		 * - Name
		 * - "Location" city
		 * - Twitter
		 * - phone number
		 * - Website
		 * - Hours
		 * - Category
		 * - make sure to add factual id
		 */

		if ($category !== '') {
			$category_ids = $category;
		} else {
			$category_ids = '308';
		}

		$query = new \FactualQuery;
		$query->field('locality')->equal($locality);
//		$query->field('category_ids')->in($category_ids);
		$query->limit(intval($limit));
		$res = $this->factual_client->fetch("places-us", $query);

		$status = (! empty($errors) ? 'failed' : 'success');

		return $res->getDataAsJSON();
	}
}