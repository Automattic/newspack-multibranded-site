import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useParams } from 'react-router-dom';

import {
	Card,
	Grid,
	Button,
	SectionHeader,
	TextControl,
	ImageUpload,
	ColorPicker,
	SelectControl,
	ActionCard,
	withWizardScreen,
	hooks,
} from 'newspack-components';

import './style.scss';

const Brand = ( { brands = [], saveBrand, fetchLogoAttachment } ) => {
	const [ brand, updateBrand ] = hooks.useObjectState();
	const [ publicPages, setPublicPages ] = useState( [] );

	const { brandId } = useParams();
	const selectedBrand = brands.find( ( { id } ) => id === Number( brandId ) );

	useEffect( () => {
		if ( selectedBrand ) {
			updateBrand( selectedBrand );
			if ( ! isNaN( selectedBrand.meta?._logo ) ) {
				fetchLogoAttachment( Number( brandId ), selectedBrand.meta?._logo );
			}
		}
	}, [ selectedBrand ] );

	const defaultHomepageURLMeta = {
		label: __( 'Default Page', 'newspack-multibranded-site' ),
		value: 0,
	};

	const getThemeColor = colorName =>
		brand.meta?._theme_colors?.find( c => colorName === c.name )?.color;

	const setThemeColor = ( name, color ) => {
		const themeColors = brand?.meta?._theme_colors ? brand?.meta?._theme_colors : [];
		const colorIndex = themeColors.findIndex( _color => name === _color.name );

		const updatedThemeColors =
			colorIndex > -1
				? themeColors.map( _color => ( name === _color.name ? { ..._color, color } : _color ) )
				: [ ...themeColors, { name, color } ];

		return updateBrand( {
			meta: {
				_theme_colors: updatedThemeColors,
			},
		} );
	};

	const fetchPublicPages = () => {
		// Limiting to 100 pages, just in case.
		apiFetch( {
			path: addQueryArgs( '/wp/v2/pages', { per_page: 100, orderby: 'title', order: 'asc' } ),
		} ).then( setPublicPages );
	};

	useEffect( fetchPublicPages, [] );

	// Brand is valid with a name and a logo.
	const isBrandValid = 0 < brand?.name?.length && 0 < brand?.meta?._logo?.id;

	const registeredThemeColors = newspack_aux_data.theme_colors;

	return (
		<Fragment>
			<SectionHeader
				title={ __( 'Brand', 'newspack-multibranded-site' ) }
				description={ __( 'Set your brand identity', 'newspack-multibranded-site' ) }
			/>
			<Grid gutter={ 32 }>
				<Grid columns={ 1 } gutter={ 16 }>
					<Card noBorder>
						<TextControl
							label={ __( 'Name', 'newspack-multibranded-site' ) }
							value={ brand.name || '' }
							onChange={ updateBrand( 'name' ) }
						/>
					</Card>
				</Grid>
				<Grid columns={ 1 } gutter={ 16 }>
					<ImageUpload
						className="newspack-brand__header__logo"
						style={ {
							...( getThemeColor( 'header_background_hex' )
								? {
										backgroundColor: getThemeColor( 'header_background_hex' ),
								  }
								: {} ),
						} }
						label={ __( 'Logo', 'newspack-multibranded-site' ) }
						image={ brand.meta?._logo }
						onChange={ _logo => updateBrand( { meta: { _logo } } ) }
					/>
				</Grid>
			</Grid>

			{ registeredThemeColors && (
				<SectionHeader
					title={ __( 'Colors', 'newspack-multibranded-site' ) }
					description={ __(
						'These are the colors you can customize for this brand in the active theme',
						'newspack-multibranded-site'
					) }
				/>
			) }

			{ registeredThemeColors &&
				registeredThemeColors.map( color => {
					return (
						<ColorPicker
							key={ color.theme_mod_name }
							label={ color.label }
							color={ getThemeColor( color.theme_mod_name ) }
							onChange={ newColor => setThemeColor( color.theme_mod_name, newColor ) }
						/>
					);
				} ) }

			<SectionHeader title={ __( 'Settings', 'newspack-multibranded-site' ) } />
			<Card noBorder>
				<SelectControl
					label={ __( 'Homepage URL', 'newspack-multibranded-site' ) }
					value={ brand.meta?._show_page_on_front || '' }
					options={ [
						defaultHomepageURLMeta,
						...publicPages.map( page => ( {
							label: page.title.rendered,
							value: Number( page.id ),
						} ) ),
					] }
					onChange={ _show_page_on_front => updateBrand( { meta: { _show_page_on_front } } ) }
				/>
			</Card>
			<ActionCard
				isMedium
				title={ __( 'Set as homepage URL', 'newspack-multibranded-site' ) }
				description={ __(
					'Whether the brand URL should be at the root of the site.',
					'newspack-multibranded-site'
				) }
				toggleChecked={ brand?.meta?._custom_url }
				toggleOnChange={ _custom_url =>
					updateBrand( { meta: { _custom_url: _custom_url ? 'yes' : 'no' } } )
				}
			/>

			<div className="newspack-buttons-card">
				<Button
					disabled={ ! isBrandValid }
					isPrimary
					onClick={ () => saveBrand( Number( brandId ), brand ) }
				>
					{ __( 'Save', 'newspack-multibranded-site' ) }
				</Button>
				<Button isSecondary href="#/">
					{ __( 'Cancel', 'newspack-multibranded-site' ) }
				</Button>
			</div>
		</Fragment>
	);
};

export default withWizardScreen( Brand );
