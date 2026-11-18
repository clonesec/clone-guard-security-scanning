/**
 * Classic JSX runtime: the automatic one needs the react-jsx-runtime script, which only exists in WordPress 6.6+.
 *
 * @jsxRuntime classic
 * @jsx createElement
 * @jsxFrag Fragment
 */
import { createElement, Fragment } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import metadata from './block.json';

// Labels and pixel sizes come from Clone_Guard_Seal (PHP), printed before this script.
const seal = window.cgssSeal || { icons: {}, sizes: {}, positions: {}, pixels: {}, imageUrl: '' };

const toOptions = ( labels ) =>
	Object.keys( labels ).map( ( value ) => ( { value, label: labels[ value ] } ) );

function Edit( { attributes, setAttributes } ) {
	const { icon, size, position } = attributes;
	const blockProps = useBlockProps( { className: 'cgss-seal' } );
	const pixels = seal.pixels[ size ] || [ 150, 54 ];

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Seal', 'clone-guard-security-scanning' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Seal', 'clone-guard-security-scanning' ) }
						value={ icon }
						options={ toOptions( seal.icons ) }
						onChange={ ( value ) => setAttributes( { icon: value } ) }
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Size', 'clone-guard-security-scanning' ) }
						value={ size }
						options={ toOptions( seal.sizes ) }
						onChange={ ( value ) => setAttributes( { size: value } ) }
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Position', 'clone-guard-security-scanning' ) }
						value={ position }
						options={ toOptions( seal.positions ) }
						onChange={ ( value ) => setAttributes( { position: value } ) }
						help={ __(
							'The seal only appears on domains with an active Clone Systems account and current scans.',
							'clone-guard-security-scanning'
						) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ /* Static preview: the live seal script needs the published page and its domain. */ }
				<img
					src={ seal.imageUrl.replace( '{icon}', icon ) }
					width={ pixels[ 0 ] }
					height={ pixels[ 1 ] }
					alt={ __( 'Clone Systems Trust Seal preview', 'clone-guard-security-scanning' ) }
				/>
				{ position !== 'relative' && (
					<p className="cgss-seal-note" style={ { fontSize: '13px', margin: '8px 0 0' } }>
						{ position === 'left'
							? __( 'On the site, the seal is fixed to the bottom left corner.', 'clone-guard-security-scanning' )
							: __( 'On the site, the seal is fixed to the bottom right corner.', 'clone-guard-security-scanning' ) }
					</p>
				) }
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
