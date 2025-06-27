<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php
	// Fetch donations (replace this with your actual logic to fetch donations).
	$donations = get_posts( array(
		'post_type'      => 'donations',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	) );


	if ( empty( $donations ) ) {
		return '<p>' . esc_html__( 'No donations found.', 'njb-customizations' ) . '</p>';
	}
	?>
	<ul class="njb-donations-list">
		<?php foreach ( $donations as $donation ) : ?>
			<?php //print_r( $donation ); ?>
			<li>
				<?php // Display the donation thumbnail, title, excerpt and amount.
				if ( has_post_thumbnail( $donation->ID ) ) {
					echo get_the_post_thumbnail( $donation->ID, 'thumbnail' );
				} ?>
				<h3><?php echo esc_html( $donation->post_title ); ?></h3>
				<?php // Display the excerpt if available.
				if ( ! empty( $donation->post_content ) ) {
					echo '<p>'. get_the_excerpt( $donation->ID ) .
					' <a class="read-more" href="'. get_permalink( $donation->ID ) . '">Read More</a></p>';
				} ?>
				<?php // Display the amount donated.
				if ( get_field( 'amount_donated', $donation->ID ) ) : 
					$amount = get_field( 'amount_donated', $donation->ID );
					$formatted_amount = number_format( (float) $amount, 2, '.', ',' ); // Format as price
				?>
					<div class="amount_donated">
						<p>
							<?php echo esc_html__( 'Amount Donated:', 'njb-customizations' ); ?> 
							<?php echo esc_html( '$' . $formatted_amount ); ?>
						</p>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
