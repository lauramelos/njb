<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Render the donated amount block.
 *
 * @param array $block The block attributes.
 * @param string $content The block content.
 */	

?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php
	$postId = $block->context['postId'];
	$donatedAmount = get_field( 'amount_donated', $postId );
	?>
	<div class="donated-amount">
		<?php if ( ! empty( $donatedAmount ) ) : ?>
			<p><?php echo sprintf( __( 'Donated Amount: <b style="font-style:normal;font-weight:700;">%s</b>', 'njb-blocks' ), $donatedAmount ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'No donations have been made yet.', 'njb-blocks' ); ?></p>
		<?php endif; ?>
	</div>
</div>
