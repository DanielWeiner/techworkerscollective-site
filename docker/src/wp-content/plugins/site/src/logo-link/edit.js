import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, __experimentalUnitControl as UnitControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<UnitControl
						label="Size"
						value={ attributes.size }
						inputMode='numeric'
						units={[
							{ label: 'px', value: 'px'},
							{ label: 'rem', value: 'rem'},
							{ label: 'em', value: 'em'},
							{ label: '%', value: '%'}
						]}
						onChange={(size) => {
							setAttributes({ size })
						}}
						min={1}
						max={100}
						step={0.5}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div style={{pointerEvents: 'none' }}>
					<ServerSideRender
						block="site/logo-link"
						attributes={ attributes }
					/>
				</div>
			</div>
		</>

	);
}
