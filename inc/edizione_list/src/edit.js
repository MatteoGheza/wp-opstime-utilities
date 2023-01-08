/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

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
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	console.log(wp.data.select('core').getEntityRecords('taxonomy', 'edizione'));

	wp.data.dispatch('core/notices').createNotice(
		'info', // Can be one of: success, info, warning, error.
		"Per ragioni di performance, la visualizzazione della lista delle edizioni nell'editor è disabilitata. Aprire l'anteprima della bozza per visualizzare.", // Text string to display.
		{
			id: 'edizione_list_notice', //assigning an ID prevents the notice from being added repeatedly
			isDismissible: false, // Whether the user can dismiss the notice.
		}
	);

	return (
		<div { ...blockProps }>
			<p>Elenco delle edizioni pubblicate. Usa la fantasia o apri l'anteprima della bozza.</p>
		</div>
	);
}
