<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Attachment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Attachment\Column\MediaEditColumn;
use WebPify\Core\BootableProviderInterface;
use WebPify\Transformer\ImageTransformerInterface;

/**
 * @package WebPify\Attachment
 */
final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ MetaDataImageGenerator::class ] = function ( Container $plugin ): MetaDataImageGenerator {

			return new MetaDataImageGenerator(
				$plugin[ ImageTransformerInterface::class ],
				wp_get_upload_dir()
			);
		};

		$plugin[ MediaEditColumn::class ] = function (): MediaEditColumn {

			return new MediaEditColumn();
		};
	}

	public function boot( Container $plugin ) {

		add_filter(
			'wp_generate_attachment_metadata',
			[ $plugin[ MetaDataImageGenerator::class ], 'generate' ],
			10,
			2
		);

		if ( is_admin() ) {

			add_filter(
				'manage_media_columns',
				[ $plugin[ MediaEditColumn::class ], 'title' ]
			);

			add_filter(
				'manage_media_custom_column',
				[ $plugin[ MediaEditColumn::class ], 'content' ], 10, 2
			);

		}

	}
}
