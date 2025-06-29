<?php
/**
 * View: Default Template for the Archive of Events on FSE
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/blocks/archive-events.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @version 5.13.0
 */

use Tribe\Events\Views\V2\Assets as Event_Assets;
use Tribe\Events\Views\V2\Template_Bootstrap;

tribe_asset_enqueue_group( Event_Assets::$group_key );
?>
<?php
// Assuming you have the block's HTML in a variable called $block_html
$block_html = '<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:template-part {"slug":"header","theme":"njb","align":"full"} /--></div>
<!-- /wp:group -->';

// Print the block
echo do_blocks( $block_html );
// Assuming you have the block's HTML in a variable called $block_html
$block_html = '<!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"accent-4","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-color has-accent-4-background-color has-text-color has-background has-link-color" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><h1 class="alignwide wp-block-post-title">Events</h1></div>
<!-- /wp:group -->';

// Print the block
echo do_blocks( $block_html );

?>
<div class="tribe-block tec-block__archive-events">
	<?php echo tribe( Template_Bootstrap::class )->get_view_html(); ?>
</div>
<?php
// Assuming you have the block's HTML in a variable called $block_html
$block_html = '<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:template-part {"slug":"footer","theme":"njb","align":"full"} /--></div>
<!-- /wp:group -->';

// Print the block
echo do_blocks( $block_html );
?>
