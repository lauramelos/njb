<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Check if we're in an order context
global $wp;

// Try to get order from various contexts
$order = null;

// Context 1: Order received page
if ( is_wc_endpoint_url( 'order-received' ) && isset( $wp->query_vars['order-received'] ) ) {
	$order_id = absint( $wp->query_vars['order-received'] );
	$order = wc_get_order( $order_id );
}

// Context 2: View order page (My Account)
if ( ! $order && is_wc_endpoint_url( 'view-order' ) && isset( $wp->query_vars['view-order'] ) ) {
	$order_id = absint( $wp->query_vars['view-order'] );
	$order = wc_get_order( $order_id );
}

// Context 3: Check if there's an order ID in the query string
if ( ! $order && isset( $_GET['order_id'] ) ) {
	$order_id = absint( $_GET['order_id'] );
	$order = wc_get_order( $order_id );
}

// If no order found, return empty
if ( ! $order ) {
	return;
}

// Check if order has subscription products using NJB_Customizations method if available
// Otherwise, check using wps_sfw_check_product_is_subscription function
$has_subscription = false;

if ( class_exists( 'NJB_Customizations' ) && method_exists( 'NJB_Customizations', 'order_has_subscription_products' ) ) {
	$has_subscription = NJB_Customizations::order_has_subscription_products( $order );
} elseif ( function_exists( 'wps_sfw_check_product_is_subscription' ) ) {
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( $product && wps_sfw_check_product_is_subscription( $product ) ) {
			$has_subscription = true;
			break;
		}
	}
}

// If no subscription products, return empty
if ( ! $has_subscription ) {
	return;
}

// Get checkout fields
$checkout_fields = Automattic\WooCommerce\Blocks\Package::container()->get( Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields::class );
$all_fields = $checkout_fields->get_all_fields_from_object( $order, 'other' );

// Get WhatsApp number and Custom Number
$whatsapp_number = ! empty( $all_fields['njb/whatsapp_number'] ) ? $all_fields['njb/whatsapp_number'] : '';
$custom_number = $order->get_meta( 'custom_number' );

// Get group options from NJB_Customizations class
$group_options = array(
	'njb/book-club'           => array( 'label' => 'Book Club',             'whatsapp' => 'LTr2ryN0Lf2BN5ARpA99qf' ),
	'njb/business-start-up'   => array( 'label' => 'Business Start Up',     'whatsapp' => 'EVK3OxGl9BMK2Wb5zyGMAi' ),
	'njb/community-outreach'  => array( 'label' => 'Community Outreach',    'whatsapp' => 'Fc793VxTBehLNwvjkfqFsq' ),
	'njb/culinary-interests'  => array( 'label' => 'Culinary Interests',    'whatsapp' => 'Gaf9QlV6nZ3Is4BqcrXR7h' ),
	'njb/fashion-lifestyle'   => array( 'label' => 'Fashion & Lifestyle',   'whatsapp' => 'LofawNEG3SVE8HvHqGvc9S' ),
	'njb/fitness-exercise'    => array( 'label' => 'Fitness & Exercise',    'whatsapp' => 'IWtXszLXxY42Yk7r5Wu7Go' ),
	'njb/travels-art-culture' => array( 'label' => 'Travels, Art & Culture','whatsapp' => 'JmP1LklPDlG0iQHJhHjyI4' ),
	'njb/young-adults'        => array( 'label' => 'Young Adults',          'whatsapp' => 'B4mmTfM8eigLmEYnccWf6E' ),
);

?>
<div class="njb-additional-fields alignwide wp-block-woocommerce-order-confirmation-billing-wrapper">
	<div class="njb-whatsapp-groups wc-block-order-confirmation-billing-address alignwide">
		<?php if ( ! empty( $whatsapp_number ) ) : ?>
			<p>
				<strong><?php esc_html_e( 'WhatsApp Number', 'njb-blocks' ); ?></strong> <?php echo esc_html( $whatsapp_number ); ?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $custom_number ) ) : ?>
			<p>
				<strong><?php esc_html_e( 'Custom Number', 'njb-blocks' ); ?></strong> <?php echo esc_html( $custom_number ); ?><br />
				<strong><?php esc_html_e( 'Note:', 'njb-blocks' ); ?></strong> <?php esc_html_e( 'This NJB ID will be required for registration at future NJB events.', 'njb-blocks' ); ?>
			</p>
		<?php endif; ?>

		<p>
			<strong><?php esc_html_e( 'Main NJB Group', 'njb-blocks' ); ?></strong><a href="https://chat.whatsapp.com/Gb5oNEguy8N1zRXIStjbh0" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Join Main Whatsapp Group', 'njb-blocks' ); ?>
			</a>
		</p>

		<?php
		$has_groups = false;

		// Check if any groups are selected
		foreach ( $group_options as $key => $value ) {
			if ( ! empty( $all_fields[ $key ] ) ) {
				$has_groups = true;
				break;
			}
		}

		if ( $has_groups ) :
		?>
			<p>
				<strong><?php esc_html_e( 'Selected Additional Groups', 'njb-blocks' ); ?></strong>
				<?php
				foreach ( $group_options as $key => $value ) {
					$group_value = $all_fields[ $key ];
					if ( ! empty( $group_value ) ) {
						echo esc_html( $value['label'] ) . ': <a href="https://chat.whatsapp.com/' . esc_html( $value['whatsapp'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Join Whatsapp Group', 'njb-blocks' ) . '</a><br />';
					}
				}
				?>
			</p>
		<?php endif; ?>
		<p><?php echo esc_html__( 'Check your email address for your login information and unique NJB ID.', 'njb-blocks' ); ?></p>
		<p><strong><?php echo esc_html__( '📌 Business Listing for Members', 'njb-blocks' ); ?></strong></p>
		<p><?php echo esc_html__( 'As an NJB member, you are eligible to list your business in our official NJB Business Directory. This gives your brand visibility within the NJB community and allows other members to easily discover and support your services.', 'njb-blocks' ); ?></p>
		<p><?php echo sprintf( __( 'You can submit your business detail here: %s', 'njb-blocks' ), '<a href="'.wc_get_account_endpoint_url( 'business' ) ).'">Business Area</a>';?></p>
	</div>
</div>
