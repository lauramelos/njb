/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<div style={{ padding: '20px', border: '1px dashed #ccc', borderRadius: '4px' }}>
				<h3>{__('Additional Fields Block', 'njb-blocks')}</h3>
				<p style={{ margin: '10px 0', color: '#666' }}>
					{__('This block will display WhatsApp groups information conditionally.', 'njb-blocks')}
				</p>
				<p style={{ margin: '10px 0', color: '#666' }}>
					{__('Groups will only show when the order contains subscription products (ID: 942).', 'njb-blocks')}
				</p>
				<div style={{ marginTop: '15px', padding: '10px', backgroundColor: '#f0f0f0', borderRadius: '3px' }}>
					<strong>{__('Preview:', 'njb-blocks')}</strong>
					<ul style={{ marginTop: '10px', paddingLeft: '20px' }}>
						<li>{__('Main NJB Group', 'njb-blocks')}</li>
						<li>{__('Selected Additional Groups', 'njb-blocks')}</li>
					</ul>
				</div>
			</div>
		</div>
	);
}
