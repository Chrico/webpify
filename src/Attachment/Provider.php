<?php declare( strict_types=1 );

namespace WebPify\Attachment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;
use WebPify\Transformer\ImageTransformerInterface;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ MetaDataImageGenerator::class ] = function ( Container $plugin ): MetaDataImageGenerator {

			return new MetaDataImageGenerator(
				$plugin[ ImageTransformerInterface::class ],
				wp_get_upload_dir()
			);
		};

	}

	public function boot( Container $plugin ) {

		add_filter(
			'wp_generate_attachment_metadata',
			[ $plugin[ MetaDataImageGenerator::class ], 'generate' ],
			10,
			2
		);

	}
}
