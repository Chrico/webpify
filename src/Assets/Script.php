<?php declare( strict_types=1 );

namespace WebPify\Assets;

/**
 * @package WebPify\Assets
 */
final class Script {

	const HANDLE = 'webpify';

	/**
	 * @var string
	 */
	private $plugin_file_path;

	/**
	 * @param string $plugin_file_path
	 */
	public function __construct( string $plugin_file_path ) {

		$this->plugin_file_path = $plugin_file_path;
	}

	public function enqueue() {

		wp_enqueue_script(
			self::HANDLE,
			$this->url(),
			[],
			NULL,
			TRUE
		);
	}

	/**
	 * @return string
	 */
	private function url(): string {

		$file_name = self::HANDLE . ( $this->is_debug() ? '.js' : '.min.js' );
		$subdir    = '/assets/js/dist';

		$url = plugins_url( "{$subdir}/{$file_name}", $this->plugin_file_path );

		return $url;

	}

	/**
	 * @return bool
	 */
	private function is_debug(): bool {

		return defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

}
