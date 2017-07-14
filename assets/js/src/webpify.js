import LazyLoad from '../../../node_modules/vanilla-lazyload/dist/lazyload.es2015';

const webp = new Image();
webp.onerror = function() {
	new LazyLoad( {
		'data_srcset': 'srcset',
		'data_src'   : 'src'
	} );
};
webp.onload = function() {
	new LazyLoad( {
		'data_srcset': 'webpSrcset',
		'data_src'   : 'webpSrc'
	} );
};
webp.src = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=';
