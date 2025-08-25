/**
 **** WARNING: No ES6 modules here. Not transpiled! ****
 */
/* eslint-disable import/no-nodejs-modules */
/* eslint-disable @typescript-eslint/no-var-requires */

/**
 * External dependencies
 */
const getBaseWebpackConfig = require( 'newspack-scripts/config/getWebpackConfig' );
const path = require( 'path' );

/**
 * Internal variables
 */
const entry = {
	admin: path.join( __dirname, 'src/admin' ),
	postPrimaryBrand: path.join( __dirname, 'src/post-primary-brand' ),
	promptBrands: path.join( __dirname, 'src/prompt-brands' ),
	linkRewriter: path.join( __dirname, 'src/link-rewriter' ),
};

const webpackConfig = getBaseWebpackConfig(
	{
		entry,
		externals: {
			'@wordpress/dom-ready': 'wp.domReady',
			'@wordpress/url': 'wp.url',
		},
	}
);

module.exports = webpackConfig;
