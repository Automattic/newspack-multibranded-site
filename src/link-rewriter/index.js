/**
 * Newspack Multibranded Site Link Rewriter.
 *
 * Rewrites internal links to include the current brand parameter.
 */

import domReady from '@wordpress/dom-ready';
import { addQueryArgs } from '@wordpress/url';

// LocalStorage key for preferred brand.
const PREFERRED_BRAND_KEY = 'np_preferred_brand';

/**
 * Get the current brand from the URL, localized data, or localStorage.
 *
 * @return {string|null} The brand slug or null if not present.
 */
const getCurrentBrand = () => {
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.get('brand')) {
		return urlParams.get('brand');
	}
	if (window.newspackBrandData?.current) {
		return window.newspackBrandData.current;
	}
	return localStorage.getItem(PREFERRED_BRAND_KEY);
};

/**
 * Persist the current brand from localized data in localStorage, if not already set.
 */
const persistBrandPreference = () => {
	if (
		window.newspackBrandData?.current &&
		!localStorage.getItem(PREFERRED_BRAND_KEY)
	) {
		localStorage.setItem(
			PREFERRED_BRAND_KEY,
			window.newspackBrandData.current
		);
	}
};

/**
 * Check if a URL is internal to the current site.
 *
 * @param {string} url The URL to check.
 * @return {boolean} Whether the URL is internal.
 */
const isInternalUrl = url => {
	try {
		const urlObj = new URL(url, window.location.origin);

		// Ignore links that go directly to the main homepage or start with /wp-admin.
		if (urlObj.pathname === '/' || urlObj.pathname.startsWith('/wp-admin')) {
			return false;
		}

		return urlObj.hostname === window.location.hostname;
	} catch (e) {
		// If URL parsing fails, assume it's not internal.
		return false;
	}
};

/**
 * Check if a URL already has a brand parameter.
 *
 * @param {string} url The URL to check.
 * @return {boolean} Whether the URL has a brand parameter.
 */
const hasBrandParam = url => {
	try {
		const urlObj = new URL(url, window.location.origin);
		return urlObj.searchParams.has('brand');
	} catch (e) {
		return false;
	}
};

/**
 * Rewrite a URL to include the brand parameter.
 *
 * @param {string} url   The URL to rewrite.
 * @param {string} brand The brand slug.
 * @return {string} The rewritten URL.
 */
const rewriteUrl = (url, brand) => {
	try {
		return addQueryArgs(url, { brand });
	} catch (e) {
		return url;
	}
};

/**
 * Initialize the link rewriter.
 */
const initLinkRewriter = () => {
	persistBrandPreference();

	const brand = getCurrentBrand();
	if (!brand) {
		return;
	}

	// Find all anchor tags in the document.
	const links = document.getElementsByTagName('a');
	for (const link of links) {
		const href = link.getAttribute('href');
		if (!href || !isInternalUrl(href) || hasBrandParam(href)) {
			continue;
		}

		// Rewrite the URL to include the brand parameter.
		link.setAttribute('href', rewriteUrl(href, brand));
	}
};

// Initialize when the DOM is ready.
domReady(initLinkRewriter);
