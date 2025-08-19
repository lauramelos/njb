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
		$business['location_state']   = array( 'label' => 'States', 'terms' => get_terms( ['taxonomy' => 'business_location_state', 'hide_empty' => true] ) );
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
					?>
					<h5><?php the_title(); ?></h5>
					<?php // Display the sector
					$sectors = get_the_terms( get_the_ID(), 'business_sector' );
					if ( $sectors && ! is_wp_error( $sectors ) ) {
						echo '<p class="business-sector">';
						foreach ( $sectors as $sector ) {
							echo esc_html( $sector->name ) . ' ';
						}
						echo '</p>';
					}
					?>
					<?php // Display the location country and state
					$country = get_the_terms( get_the_ID(), 'business_location_country' );
					$state = get_the_terms( get_the_ID(), 'business_location_state' );
					if ( $country && ! is_wp_error( $country ) ) { ?>
						<p class="business-location">
							<?php
							foreach ( $state as $s ) {
								echo esc_html( $s->name );
							}
							if ( $state && ! is_wp_error( $state ) && $country && ! is_wp_error( $country ) ) {
								echo ', ';
							}
							foreach ( $country as $c ) {
								echo esc_html( $c->name );
							} ?>
						</p>
					<?php } ?>
				</div>
			<?php endwhile;
		else :
			echo '<p>No results found.</p>';
		endif; ?>
	</div>
	<?php wp_reset_postdata(); ?>
</div>
