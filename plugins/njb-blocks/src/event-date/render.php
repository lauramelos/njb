<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div class="wp-block-post-date">
	<time datetime="<?php echo esc_attr( tribe_get_start_date( $block->context['postId'], false, 'c' ) ); ?>">
		<?php
		$event = $block->context['postId'];
		if ( ! tribe_is_event( $event ) ){
			return false;
		}
		
		$event = tribe_events_get_event( $event );
		
		echo esc_html( tribe_get_start_date( $event, false, 'F j, Y' ) );
		?>
	</time>
</div>
