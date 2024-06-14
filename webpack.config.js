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
const admin = path.join( __dirname, 'src/admin' );
const postPrimaryBrand = path.join( __dirname, 'src/post-primary-brand' );
const promptBrands = path.join( __dirname, 'src/prompt-brands' );

const webpackConfig = getBaseWebpackConfig(
	{
		entry: { admin, postPrimaryBrand, promptBrands },
	}
);

module.exports = webpackConfig;
