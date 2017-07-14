/*global require */
/*eslint no-console: 1 */
"use strict";
const gulp = require( 'gulp' );
const uglify = require( 'gulp-uglify' );
const rename = require( 'gulp-rename' );
const browserify = require( 'gulp-browserify' );

const ASSET_DIR = 'assets/';
const CONF = {
	js: {
		src : ASSET_DIR + 'js/src/',
		dest: ASSET_DIR + 'js/dist/'
	}
};

gulp.task( 'scripts', function() {
	gulp.src( CONF.js.src + '*.js' )
		.pipe( browserify( {
			insertGlobals: false,
			debug        : true
		} ) )
		.pipe( gulp.dest( CONF.js.dest ) )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( uglify( {
			output: {
				ascii_only: true
			}
		} ) )
		.pipe( gulp.dest( CONF.js.dest ) );
} );

// Main task
gulp.task( 'default', [ 'scripts' ] );