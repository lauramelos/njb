<?php  wp_interactivity_state( 'create-block', array()); ?>

<div
    <?php echo get_block_wrapper_attributes(  array( 'isOpen' => false ) ); ?>
    data-wp-interactive="create-block" 
    <?php echo wp_interactivity_data_wp_context( array( 'isOpen' => false ) ); ?>
        data-wp-on-document--keydown="callbacks.handleEscClose"
        data-wp-on-document--click="callbacks.handleClickOutside"
    >
    <?php if ( ! empty( $content ) ) { 
        $button_text = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Show Modal', 'njb-blocks' );
        ?>
        <button class="wp-element-button js-show-modal " data-wp-on--click="actions.openModal"> 
            <?php echo esc_html( $button_text ); ?>
        </button>
        <div class="container">
        <div class="modal js-modal" data-wp-class--modal--opened="context.isOpen">
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
