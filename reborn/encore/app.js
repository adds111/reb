// assets/app.js

// import { registerReactControllerComponents } from '@symfony/ux-react';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// registerReactControllerComponents(require.context('./react/controllers', true, /\.(j|t)sx?$/));

import './styles/app.scss';

import '@popperjs/core';

// start the Stimulus application
import './bootstrap';

/** Bootstrap Elements **/
require('bootstrap/js/src/carousel');
require('bootstrap/js/src/modal');
require('bootstrap/js/src/tooltip');
require('bootstrap/dist/js/bootstrap');

// import 'animate.css';

import './scripts/plugins/Yandex.Metrika'

import './scripts/Climb';
import './scripts/Buttons'
import './scripts/pdfjs-view';
import './scripts/ProductCart';
import './scripts/ModelView';
import './scripts/ImageSlider';
import './scripts/orderRequest';
import './scripts/StuffInfo';
import './scripts/calcs/translation-of-units-of-measurement';
import './scripts/calcs/cv-and-expense-calculator';

import './scripts/snippets/ProductsVideo'
import './scripts/snippets/WebinarWatchers';
import './scripts/snippets/Navbar';

import './scripts/table/Inventory';
import './scripts/Search';

// import './scripts/cdn.jsdelivr.net_npm_select2@4.1.0-rc.0_dist_js_select2.min';