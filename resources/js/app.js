/**
 * Single bundle for every layout. Chart.js used to arrive from jsDelivr and be
 * read off `window`; the console dashboard still expects `window.Chart`, so we
 * put it there deliberately instead of rewriting every inline chart block.
 */
import Chart from 'chart.js/auto';

window.Chart = Chart;

import './ivora.js';
