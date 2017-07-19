<?php declare( strict_types=1 );

namespace WebPify\Assets;

class Script {

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

		list( $path, $url ) = $this->path_and_url();
		wp_enqueue_script( self::HANDLE, $url, [], NULL, TRUE );
	}

	/**
	 * @return array
	 */
	private function path_and_url(): array {

		$file_name   = self::HANDLE;
		$subdir      = '/assets/js/dist';
		$folder_path = dirname( $this->plugin_file_path ) . $subdir;

		$use_min   = ! $this->is_debug() && file_exists( "{$folder_path}/{$file_name}.min.js" );
		$file_name .= $use_min ? '.min.js' : '.js';

		$url = plugins_url( "{$subdir}/{$file_name}", $this->plugin_file_path );

		return [ "{$folder_path}/{$file_name}", $url ];

	}

	/**
	 * @return bool
	 */
	private function is_debug(): bool {

		return defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

}
