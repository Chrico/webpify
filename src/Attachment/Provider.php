<?php declare( strict_types=1 );

namespace WebPify\Attachment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;
use WebPify\Transformer\NativeExtensionTransformer;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ WebPImageGenerator::class ] = function ( Container $plugin ): WebPImageGenerator {

			return new WebPImageGenerator(
				$plugin[ NativeExtensionTransformer::class ],
				wp_get_upload_dir()
			);
		};

	}

	public function boot( Container $plugin ) {

		add_filter(
			'wp_generate_attachment_metadata',
			[ $plugin[ WebPImageGenerator::class ], 'generate' ],
			10,
			2
		);

	}
}
