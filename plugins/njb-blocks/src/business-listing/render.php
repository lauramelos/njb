<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
wp_enqueue_script('select2');
wp_enqueue_style('select2');
if ( ! function_exists( 'njb_get_filters' ) ) {
	/**
	 * Outputs the filters for the custom post type listing.
	 *
	 * @return void
	 */
	function njb_get_filters() {
		// Example: Dropdown filter for an ACF field "genre"
		$business['sector']           = array( 'label' => 'Sectors', 'terms' => get_terms( ['taxonomy' => 'business_sector', 'hide_empty' => true] ) );
		$business['location_country'] = array( 'label' => 'Countries', 'terms' => get_terms( ['taxonomy' => 'business_location_country', 'hide_empty' => true] ) );
		//$business['location_state']   = array( 'label' => 'States', 'terms' => get_terms( ['taxonomy' => 'business_location_state', 'hide_empty' => true] ) );
		?>
		<form id="cpt-filters" class="business-filters" >
			<input type="text" name="s" class="wpforms-field-medium" placeholder="Search..." />
			<?php foreach ( $business as $key => $filter ) : ?>
				<?php if ( ! empty( $filter['terms'] ) ) : ?>
					<select name="business_<?php echo esc_attr($key); ?>" class="select2">
						<option value="">All <?php echo $filter['label']; ?></option>
						<?php foreach( $filter['terms'] as $g ): ?>
							<option value="<?php echo esc_attr($g->slug); ?>"><?php echo esc_html($g->name); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			<?php endforeach; ?>

			<button type="submit" class="wp-block-button wp-element-button">Filter</button>
		</form>
		<?php
	}
}
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php njb_get_filters(); ?>
	<div id="cpt-listing-results" class="business-listing">
	<?php
		if ( ! function_exists( 'get_field' ) ) return;
		$query = new WP_Query([
			'post_type' => 'business',
			'posts_per_page' => 10,
		]);
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post(); 
				if ( ! get_field('approved') ) continue; // Skip if not approved ?>
				
				<div class="business-listing-item">
					<?php
					// Display logo on custom fields if available
					if ( get_field( 'small_business_logoicon' ) ) {
						echo '<div class="business-logo">' . wp_get_attachment_image( get_field('small_business_logoicon'), 'medium' ) . '</div>';
					}
					// Display the location country and state
					$address_group = get_field('address');
					$street_address = $address_group['street_address'];
					if ( ! empty ( $street_address ) ) {
						$address['street_address'] = $street_address;
					}
					$city = $address_group['city'];
					if ( ! empty ( $city ) ) {
						if( ! empty( $address['street_address'] ) ) {
							$address['street_address'] .= ' ';
						}
						$address['street_address'] .= $city;
					}
					$postal_code = $address_group['postal_code'];
					if ( ! empty( $postal_code ) ) {
						if( ! empty( $address['street_address'] ) ) {
							$address['street_address'] .= ' ';
						}
						$address['street_address'] .=  $postal_code;
					}
				
					$state         = get_the_terms( get_the_ID(), 'business_location_state' );
					$country       = get_the_terms( get_the_ID(), 'business_location_country' );
					// map state and country into string of names
					if ( ! empty ( $state ) && !is_wp_error( $state ) ) 
						$address['state'] = implode(', ', array_map(function($s){ return $s->name; }, $state ) );
					if ( ! empty ( $country ) && !is_wp_error( $country ) )
						$address['country'] = implode(', ', array_map(function($c){ return $c->name; }, $country ) );
		
					$phone_number = get_field('phone_number');
					$email = get_field('email');
					$website = get_field('website');

					if ( $website ) : ?>
						<h5 class="business-website"><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a></h5>
					<?php else : ?>
						<h5><?php the_title(); ?></h5>					
					<?php endif; ?>
					
					<?php // Display the sector
					$sectors = get_the_terms( get_the_ID(), 'business_sector' );
					if ( $sectors && ! is_wp_error( $sectors ) ) {
						echo '<p class="business-sector">';
						foreach ( $sectors as $sector ) {
							echo esc_html( $sector->name ) . ' ';
						}
						echo '</p>';
					}
					if ( ! empty( $address ) ) { ?>
						<p class="business-location">
							<span class="dashicons dashicons-location"></span>
							<?php echo implode(	', ', $address ) ?>
						</p>
					<?php } ?>
					<?php if ( $phone_number ) : ?>
						<p class="business-phone">
							<span class="dashicons dashicons-phone"></span>
							<a href="tel:<?php echo esc_attr( preg_replace('/\s+/', '', $phone_number) ); ?>"><?php echo esc_html( $phone_number ); ?></a>
						</p>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<p class="business-email">
							<span class="dashicons dashicons-email"></span>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</p>
					<?php endif; ?>
				
				</div>
			<?php endwhile;
		else :
			echo '<p>No results found.</p>';
		endif; ?>
	</div>
	<?php wp_reset_postdata(); ?>
</div>
