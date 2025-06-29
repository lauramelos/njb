<?php
/**
 * View: Default Template for the Single Events on FSE.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/blocks/single-event.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @version 6.2.7
 */

use Tribe\Events\Views\V2\Assets as Event_Assets;
use Tribe\Events\Views\V2\Template_Bootstrap;

tribe_asset_enqueue_group( Event_Assets::$group_key );
// Assuming you have the block's HTML in a variable called $block_html
$block_html = '<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:template-part {"slug":"header","theme":"njb","align":"full"} /--></div>
<!-- /wp:group -->';

// Print the block
echo do_blocks( $block_html );
?>
<div class="tribe-block tec-block__single-event">
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