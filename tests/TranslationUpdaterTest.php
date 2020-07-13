<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Test;

use Antenna\InlineTranslations\Exceptions\InvalidKeyException;
use Antenna\InlineTranslations\TranslationUpdater;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class TranslationUpdaterTest extends TestCase
{
    private TranslationUpdater $updater;

    public function setUp() : void
    {
        $adapter = new NullAdapter();
        $adapter->setPathPrefix('tests/translations');

        $filesystem    = new Filesystem($adapter);
        $this->updater = new TranslationUpdater($filesystem);
    }

    public function testInvalidKey() : void
    {
        $this->expectException(InvalidKeyException::class);
        $this->updater->updateTranslation('key', 'value', 'nl');
    }

    public function testUpdatingTranslation() : void
    {
        $result = $this->updater->updateTranslation('app.key', 'value', 'nl');
        $this->assertTrue($result);
    }

    public function testDeletingTranslation() : void
    {
        $result = $this->updater->removeTranslation('app.holidays.january.newyear', 'nl');
        $this->assertTrue($result);
    }
}
