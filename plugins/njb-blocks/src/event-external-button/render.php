<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php
	$event = $block->context['postId'];
	if ( ! tribe_is_event( $event ) ){
		return false;
	}
	$event = tribe_events_get_event( $event );
	$link = get_field('external_link');
	$text = __('Register to Event', 'njb-blocks');
    if ( time() < strtotime( $event->end_date )  ) { 
		$link = get_permalink( $event->ID );
		$text = __('View Past Event', 'njb-blocks');
    } elseif ( empty( $link ) ) {
		$link = get_permalink( $event->ID );
		$text = __('View Event', 'njb-blocks');
	} 
	
    ?>
    <div class="wp-block-buttons is-layout-flex  wp-block-buttons-is-layout-flex">
        <div class="wp-block-button">
            <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($link) ?>"><?php echo esc_html($text) ?></a>
        </div>
    </div>
</div>
