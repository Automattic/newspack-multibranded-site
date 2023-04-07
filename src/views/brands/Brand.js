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
		label: __( 'Default Page', 'newspack' ),
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

	return (
		<Fragment>
			<SectionHeader
				title={ __( 'Brand', 'newspack' ) }
				description={ __( 'Set your brand identity', 'newspack' ) }
			/>
			<Grid gutter={ 32 }>
				<Grid columns={ 1 } gutter={ 16 }>
					<Card noBorder>
						<TextControl
							label={ __( 'Name', 'newspack' ) }
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
						label={ __( 'Logo', 'newspack' ) }
						image={ brand.meta?._logo }
						onChange={ _logo => updateBrand( { meta: { _logo } } ) }
					/>
				</Grid>
			</Grid>

			<SectionHeader
				title={ __( 'Colors', 'newspack' ) }
				description={ __( 'Pick your primary and secondary colors', 'newspack' ) }
			/>
			<Grid gutter={ 32 }>
				<ColorPicker
					label={ __( 'Primary' ) }
					color={ getThemeColor( 'primary_color_hex' ) }
					onChange={ primary_color_hex => setThemeColor( 'primary_color_hex', primary_color_hex ) }
				/>
				<ColorPicker
					label={ __( 'Secondary' ) }
					color={ getThemeColor( 'secondary_color_hex' ) }
					onChange={ secondary_color_hex =>
						setThemeColor( 'secondary_color_hex', secondary_color_hex )
					}
				/>
			</Grid>

			<SectionHeader
				title={ __( 'Background colors', 'newspack' ) }
				description={ __( 'Pick your header and footer backgrounds', 'newspack' ) }
			/>
			<Grid gutter={ 32 }>
				<ColorPicker
					label={ __( 'Header Background' ) }
					color={ getThemeColor( 'header_background_hex' ) }
					onChange={ header_background_hex =>
						setThemeColor( 'header_background_hex', header_background_hex )
					}
				/>
				<ColorPicker
					label={ __( 'Footer Background' ) }
					color={ getThemeColor( 'footer_background_hex' ) }
					onChange={ footer_background_hex =>
						setThemeColor( 'footer_background_hex', footer_background_hex )
					}
				/>
			</Grid>

			<SectionHeader title={ __( 'Settings', 'newspack' ) } />
			<Card noBorder>
				<SelectControl
					label={ __( 'Homepage URL', 'newspack' ) }
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
				title={ __( 'Set as homepage URL', 'newspack' ) }
				description={ __( 'Whether the brand URL should be at the root of the site.', 'newspack' ) }
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
					{ __( 'Save', 'newspack' ) }
				</Button>
				<Button isSecondary href="#/">
					{ __( 'Cancel', 'newspack' ) }
				</Button>
			</div>
		</Fragment>
	);
};

export default withWizardScreen( Brand );
