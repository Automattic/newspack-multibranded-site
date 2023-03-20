import domReady from '@wordpress/dom-ready';
import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter as Router, Routes, Route } from 'react-router-dom';
import BrandsList from './views/BrandsList';
import './index.scss';

const App = () => (
	<Router>
		<div className="mt4">
			<Routes>
				<Route path="/" element={ <BrandsList /> } />
			</Routes>
		</div>
	</Router>
);

domReady( () => {
	ReactDOM.render(
		<React.StrictMode>
			<App />
		</React.StrictMode>,
		document.getElementById( 'root' )
	);
} );
