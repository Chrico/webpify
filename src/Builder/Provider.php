<?php declare( strict_types=1 );

namespace WebPify\Builder;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;
use WebPify\Model\WebPImage;
use WebPify\Transformer\NativeExtensionTransformer;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ WebPImageBuilder::class ] = function ( Container $plugin ): WebPImageBuilder {

			return new WebPImageBuilder(
				$plugin[ NativeExtensionTransformer::class ],
				wp_get_upload_dir()
			);
		};

	}

	public function boot( Container $plugin ) {

		add_filter(
			'wp_generate_attachment_metadata',
			function ( array $metadata, $attachment_id ) use( $plugin ): array {

				$builder = $plugin[ WebPImageBuilder::class ];

				update_post_meta(
					$attachment_id,
					WebPImage::ID,
					$builder->build( $metadata )
				);

				return $metadata;
			},
			10,
			2
		);

	}
}
