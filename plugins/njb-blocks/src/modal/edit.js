/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, useInnerBlocksProps, InnerBlocks,InspectorControls } from '@wordpress/block-editor';
 
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */

import { useState } from 'react';
import { Button, Modal, PanelBody, TextControl } from '@wordpress/components';

export default function Edit( props  ) {
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps );
	const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );

	function updateButtonText(value) {
		props.setAttributes({ buttonText: value });
	}
    return (
		 <div {...innerBlocksProps}>
			<InspectorControls>
                <PanelBody title={ __( 'Button Settings', 'njb-blocks' ) }>
                    <TextControl
                        label={ __( 'Button Text', 'njb-blocks' ) }
                        value={ props.attributes.buttonText }
                        onChange={ ( value ) => updateButtonText( value ) }
                        placeholder={ __( 'Enter button text...', 'njb-blocks' ) }
                    />
                </PanelBody>
            </InspectorControls>
			<Button className="wp-modal-button" onClick={ openModal }>
				{ props.attributes.buttonText || __( 'Open Modal', 'njb-blocks' ) }
            </Button>
            { isOpen && (
                <Modal onRequestClose={ closeModal }>
                    <Button variant="secondary" onClick={ closeModal }>
					<InnerBlocks
						allowedBlocks={ [ 'core/button', 'core/paragraph', 'core/image', 'core/heading', 'core/list', 'core/shortcode', 'wpforms/form-selector', ] }
						template={ [
							[ 'core/group', { lock: { move: true, remove: true }, tagName: 'div', className: 'modal-content' } , 
								[[ 'core/paragraph', { placeholder: __( 'Add your modal content here...', 'njb-blocks' ) } ]],
							],
						]}
					/>
                    </Button>
                </Modal>
            ) }
			<InnerBlocks
						allowedBlocks={ [ 'core/button', 'core/paragraph', 'core/image', 'core/heading', 'core/list', 'core/shortcode', 'wpforms/form-selector', ] }
						template={ [
							[ 'core/group', { lock: { move: true, remove: true }, tagName: 'div', className: 'modal-content' } , 
								[[ 'core/paragraph', { placeholder: __( 'Add your modal content here...', 'njb-blocks' ) } ]],
							],
						]}
					/>
		 </div>
    );
}
