/*! global WebPify */
import LazyLoad from 'vanilla-lazyload';

if ( typeof window.CustomEvent !== "function" ) {

	function CustomEvent( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };
		let evt = document.createEvent( "CustomEvent" );
		evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;
	window.CustomEvent = CustomEvent;
}

const replaceAttribute = ( $el, from, to ) => {
	if ( !$el.hasAttribute( from ) ) {
		return false;
	}
	$el.setAttribute( to, $el.getAttribute( from ) );
	$el.removeAttribute( from );
	return true;
};

// detect if a image has the "data-web-src"- or "data-web-srcset"-attributes
// and replace the default "data-src" and/or "data-srcset"-attributes with it.
const setWebPIfExists = ( cb ) => {
	[].forEach.call(
		document.querySelectorAll( WebPify.options.elements_selector ),
		$el => {
			replaceAttribute( $el, 'data-webp-src', 'data-src' );
			replaceAttribute( $el, 'data-webp-srcset', 'data-srcset' );
		}
	);
	cb();
};

// create instance of LazyLoad.
const initializeLazyLoad = () => {
	new LazyLoad( WebPify.options || {} );
};

const webp = new Image();
webp.onerror = initializeLazyLoad;
webp.onload = function() {
	setWebPIfExists( initializeLazyLoad );
};
webp.src = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=';
