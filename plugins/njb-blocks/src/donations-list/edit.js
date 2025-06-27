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
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
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
 * @return {Element} Element to render.
 */
export default function Edit() {
	/**
	 * The useSelect hook allows you to retrieve data from the WordPress data store.
	 * In this case, it retrieves the donations post type records.
	 *
	 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-data/#useselect
	 */


	const blockProps = useBlockProps();
	const donations = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'donations',{
			_embed: true, // Include embedded data (e.g., featured media).
			_fields: 'id,title,content,link,acf', // Explicitly request the ACF field.

		} );
	}, [] );

	return (
		<div { ...blockProps }><ul>
			{ ! donations && 'Loading' }
			{ donations && donations.length === 0 && 'No donations' }
			{ donations && donations.length > 0 && (

				// display all donations
				console.log( donations[0] ),
				donations.map( ( donation ) => (
					<li key={ donation.id }>	
						{ donation._embedded && donation._embedded['wp:featuredmedia'] && (
							<img
								src={ donation._embedded['wp:featuredmedia'][0].source_url }
								alt={ donation._embedded['wp:featuredmedia'][0].alt_text || donation.title.rendered }
								style={ { maxWidth: '100px', height: 'auto' } }
							/>
						) }
						
						<h3>{ donation.title.rendered }</h3>

						<p>
							<span
								dangerouslySetInnerHTML={{
									__html: donation.content.rendered
										.replace(/<[^>]+>/g, '')
										.substring(0, 200) + '...',
								}}
							> </span>
							{donation.link && (
								<a
									href={donation.link}
									target="_blank"
									rel="noopener noreferrer"
								>
									{__('Read More', 'njb-blocks')}
								</a>
							)}
						</p>
						{ donation.acf && donation.acf.amount_donated && (
							<div className="amount_donated">
								<p>
									{ __( 'Donation Amount:', 'njb-blocks' ) } { donation.acf.amount_donated }
								</p>
							</div>
						) }
					</li>
				) )
			) }
		</ul></div>
	);
}
