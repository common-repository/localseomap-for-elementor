/**
 * BLOCK: Basic
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 *
 * Styles:
 *        editor.css — Editor styles for the block.
 *        style.css  — Editor & Front end styles for the block.
 */
( function() {
	var __                = wp.i18n.__; // The __() function for internationalization.
	var createElement     = wp.element.createElement; // The wp.element.createElement() function to create elements.
	var registerBlockType = wp.blocks.registerBlockType; // The registerBlockType() function to register blocks.
	var Editable          = wp.blocks.Editable; // For creating editable elements.
	var BlockControls     = wp.blocks.BlockControls; // For adding control elements.
	var AlignmentToolbar  = wp.blocks.AlignmentToolbar; // For creating the alignment toolbar element within the control elements.

	/**
	 * Register Basic Block.
	 *
	 * Registers a new block provided a unique name and an object defining its
	 * behavior. Once registered, the block is made available as an option to any
	 * editor interface where blocks are implemented.
	 *
	 * @param  {string}   name     Block name.
	 * @param  {Object}   settings Block settings.
	 * @return {?WPBlock}          The block, if it has been successfully
	 *                             registered; otherwise `undefined`.
	 */
	registerBlockType( 'gb/basic-01', { // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
		title: __( 'TEST', 'GB' ), // Block title.
		icon: 'shield-alt', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
		category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
		attributes: {
			content: {
				type: 'string',
				default: 'Block content can be aligned with toolbar.',
			},
			alignment: {
				type: 'string',
			},
		},
		// The "edit" property must be a valid function.
		edit: function( props ) {

			var content = props.attributes.content;
			var alignment = props.attributes.alignment;
			var focus = props.focus;

			function onChangeContent( updatedContent ) {
				props.setAttributes( { content: updatedContent } );
			}

			function onChangeAlignment( updatedAlignment ) {
				props.setAttributes( { alignment: updatedAlignment } );
			}

			return  '<div>{ attributes.content }</div>';
		},

		// The "save" property must be specified and must be a valid function.
		save: function( props ) {
			var content = props.attributes.content;
			var alignment = props.attributes.alignment;

			return createElement(
				'p',
				{
					className: props.className,
					style: { textAlign: alignment },
				},
				content
			);
		},
	} );
})();
