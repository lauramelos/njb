<?php  wp_interactivity_state( 'create-block', array( 'isOpen' => false )); ?>

<div
    <?php echo get_block_wrapper_attributes(); ?>
    data-wp-interactive="create-block" 
    data-wp-on-document--keydown="callbacks.handleEscClose"
    data-wp-on-document--click="callbacks.handleClickOutside"
    >
    <?php if ( ! empty( $content ) ) { 
        $button_text = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Show Modal', 'njb-blocks' );
        ?>
        <div class="container">
            <div class="modal js-modal" data-wp-class--modal--opened="state.isOpen">
                <div class="modal__overlay"></div>
                <div class="modal__content">
                    <button class="modal__close js-close-modal" data-wp-on--click="actions.closeModal">X
                        <i class="bx bx-x-circle"></i>
                    </button>
                    <?php echo $content; ?>
                    <div class="modal__action">
                        <button class="btn js-close-modal" data-wp-on--click="actions.closeModal">Close</button>
                    </div>
                </div>

            </div>
         </div>
    <?php } ?>
</div>

<?php
function my_custom_render( $block_content, $block ) {

    if ( $block['blockName'] !== 'core/button' ) {
        return $block_content;
    }

    $p = new WP_HTML_Tag_Processor( $block_content );

    if ( ! $p->next_tag( array( 'class_name' => 'js-show-modal' ) ) ) {
        return $block_content;
    }

    $p->set_attribute( 'data-wp-interactive', 'create-block' );
    $p->set_attribute( 'data-wp-on--click', 'core/navigation::actions.closeMenuOnClick' );
    $p->set_attribute( 'data-wp-on--click', 'actions.openModal' );

    return $p->get_updated_html();
}

add_filter( 'render_block', 'my_custom_render', 10, 2 );
