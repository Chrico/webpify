<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Assets;

/**
 * @package WebPify\Assets
 */
final class LazyLoadScriptData {

	const FILTER_OPTIONS = 'WebPify.script.options';

	/**
	 * @link https://github.com/verlok/lazyload#options
	 *
	 * @return array
	 */
	public function get_options(): array {

		return apply_filters(
			self::FILTER_OPTIONS,
			[
				'elements_selector' => "img",
				'threshold'         => 300,
				'data_src'          => "src",
				'data_srcset'       => "srcset",
				'class_loading'     => "WebPify--loading",
				'class_loaded'      => "WebPify--loaded",
				'class_error'       => "WebPify--error"
			]
		);
	}

}
