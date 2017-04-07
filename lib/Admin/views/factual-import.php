<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form id="factual-importer" method="post" action="">
		<?php wp_nonce_field( 'factual-import', 'fetch-listings' ); ?>
		<input type="hidden" name="action" value="get_factual_data">
		<div class="universal-message-container">
			<h2>Populate Database With Factual Data</h2>
			<p>This plugin will grab a number of restaurants from the Factual API at a given zipcode and import them into Listly. </p>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="factual_locality">Locality to search for listing</label>
						</th>
						<td>
							<input name="factual_locality" id="factual_locality" class="regular-text" type="text">
						</td>
					</tr>
					<tr>
						<th>
							<label for="factual_limit">Number of Factual listings to import</label>
						</th>
						<td>
							<input name="factual_limit" type="number" step="1" min="1" id="factual_limit" value="10" class="small-text"> listings
						</td>
					</tr>
					<tr>
						<th>
							<label for="factual_category">Business category to search by</label>
						</th>
						<td>
							<input name="factual_category" id="factual_category" class="regular-text" type="text">
						</td>
					</tr>
				</tbody>
			</table>
			<?php

				submit_button( 'Fetch Listing' );
			?>
		</div>
	</form>
	<div class="">
		<table class="js-factual-results">
			<thead>
				<tr>
					<th></th>
					<th>Facutal Id</th>
					<th>Category Labels</th>
					<th>Name</th>
				</tr>
			</thead>
			<tfoot>
			<tr>
				<th></th>
				<th>Facutal Id</th>
				<th>Category Labels</th>
				<th>Name</th>
			</tr>
			</tfoot>
		</table>
	</div>
</div>
