<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Attachment;

use Brain\Monkey\Functions;
use WebPify\Attachment\AttachmentDeletor;
use WebPify\Tests\Unit\AbstractTestCase;

/**
 * @package WebPify\Tests\Unit\Attachment
 */
final class AttachmentDeletorTest extends AbstractTestCase
{

    public function test_basic()
    {

        Functions\expect('wp_get_upload_dir')
            ->once();

        static::assertInstanceOf(AttachmentDeletor::class, new AttachmentDeletor());
    }

}