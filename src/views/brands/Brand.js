import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs, cleanForSlug } from '@wordpress/url';
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
	RadioControl,
	withWizardScreen,
	hooks,
} from 'newspack-components';

import './style.scss';

const Brand = ( { brands = [], saveBrand, fetchLogoAttachment } ) => {
	const [ brand, updateBrand ] = hooks.useObjectState( { slug: '', meta: { _custom_url: 'yes' } } );
	const [ publicPages, setPublicPages ] = useState( [] );
	const [ showOnFrontSelect, setShowOnFrontSelect ] = useState( 'no' );

	const { brandId } = useParams();
	const selectedBrand = brands.find( ( { id } ) => id === Number( brandId ) );

	useEffect( () => {
		if ( selectedBrand ) {
			updateBrand( selectedBrand );
			if ( ! isNaN( selectedBrand.meta._logo ) ) {
				fetchLogoAttachment( Number( brandId ), selectedBrand.meta._logo );
			}
		}
	}, [ selectedBrand ] );

	const getThemeColor = colorName =>
		brand.meta._theme_colors?.find( c => colorName === c.name )?.color;

	const setThemeColor = ( name, color ) => {
		const themeColors = brand?.meta._theme_colors ? brand?.meta._theme_colors : [];
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

	const updateSlugFromName = e => {
		if ( '' === brand.slug ) {
			updateBrand( { slug: cleanForSlug( e.target.value ) } );
		}
	};

	const updateShowOnFront = value => {
		if ( 'no' === value ) {
			updateBrand( { meta: { ...brand.meta, _show_page_on_front: 0 } } );
		}
		setShowOnFrontSelect( value );
	};

	const baseUrl = `${ newspack_urls.site }/${ 'no' === brand.meta._custom_url ? 'brand/' : '' }`;

	const fetchPublicPages = () => {
		// Limiting to 100 pages, just in case.
		apiFetch( {
			path: addQueryArgs( '/wp/v2/pages', { per_page: 100, orderby: 'title', order: 'asc' } ),
		} ).then( setPublicPages );
	};

	useEffect( fetchPublicPages, [] );

	// Brand is valid when it has a name, and if a page is selected to be shown in front, the page should be selected.
	const isBrandValid =
		0 < brand.name?.length &&
		( 'no' === showOnFrontSelect ||
			( 'yes' === showOnFrontSelect && 0 < brand.meta._show_page_on_front ) );

	return (
		<Fragment>
			<SectionHeader
				title={ __( 'Brand', 'newspack' ) }
				description={ __( 'Set your brand identity', 'newspack' ) }
			/>
			<Grid gutter={ 32 }>
				<Grid columns={ 1 } gutter={ 16 }>
					<TextControl
						label={ __( 'Name', 'newspack' ) }
						value={ brand.name || '' }
						onChange={ updateBrand( 'name' ) }
						onBlur={ updateSlugFromName }
					/>
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
						image={ brand.meta._logo }
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
				<RadioControl
					className="newspack-brand__base-url-radio-control"
					label={ __( 'URL Base', 'newspack' ) }
					selected={ brand?.meta._custom_url || 'yes' }
					options={ [
						{ label: __( 'Homepage', 'newspack' ), value: 'yes' },
						{ label: __( 'Default', 'newspack' ), value: 'no' },
					] }
					onChange={ _custom_url => updateBrand( { meta: { _custom_url } } ) }
				/>
				<div className="newspack-brand__base-url-component">
					<span>{ baseUrl }</span>
					<TextControl
						className="newspack-brand__base-url-component__text-control"
						label={ __( 'Slug', 'newspack' ) }
						hideLabelFromVision
						withMargin={ false }
						value={ brand.slug || '' }
						onChange={ updateBrand( 'slug' ) }
					/>
				</div>
			</Card>

			<Card noBorder>
				<RadioControl
					className="newspack-brand__base-url-radio-control"
					label={ __( 'Show on Front', 'newspack' ) }
					selected={ showOnFrontSelect }
					options={ [
						{ label: __( 'Latest posts', 'newspack' ), value: 'no' },
						{ label: __( 'A page', 'newspack' ), value: 'yes' },
					] }
					onChange={ value => updateShowOnFront( value ) }
				/>
				{ 'yes' === showOnFrontSelect && (
					<SelectControl
						label={ __( 'Homepage URL', 'newspack' ) }
						value={ brand.meta._show_page_on_front || 0 }
						options={ [
							{
								label: __( 'Select a Page', 'newspack' ),
								value: 0,
								disabled: true,
							},
							...publicPages.map( page => ( {
								label: page.title.rendered,
								value: Number( page.id ),
							} ) ),
						] }
						onChange={ _show_page_on_front => updateBrand( { meta: { _show_page_on_front } } ) }
						required
					/>
				) }
			</Card>

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
