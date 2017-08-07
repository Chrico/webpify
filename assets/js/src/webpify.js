import LazyLoad from '../../../node_modules/vanilla-lazyload/dist/lazyload.es2015';

// detect if a image has the "data-web-src"- or "data-web-srcset"-attributes
// and replace the default "data-src" and/or "data-srcset"-attributes with it.
const maybeReplaceDefaultImages = () => {
	[].forEach.call(
		document.querySelectorAll( 'img' ),
		$el => {
			const attributes = [
				{
					'to'  : 'data-src',
					'from': 'data-webp-src'
				},
				{
					'to'  : 'data-srcset',
					'from': 'data-webp-srcset'
				}
			];
			attributes.forEach( search => {
				if ( $el.hasAttribute( search.from ) ) {
					$el.setAttribute( search.to, $el.getAttribute( search.from ) );
					$el.removeAttribute( search.from );
				}
			} );
		}
	);
};

const initializeLazyLoad = () => {

	new LazyLoad( {
		'data_srcset': 'srcset',
		'data_src'   : 'src'
	} );
};

const webp = new Image();
webp.onerror = initializeLazyLoad;
webp.onload = function() {
	maybeReplaceDefaultImages();
	initializeLazyLoad();
};
webp.src = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=';
