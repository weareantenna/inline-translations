<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Test;

use Antenna\InlineTranslations\Exceptions\InvalidKeyException;
use Antenna\InlineTranslations\FileTranslationUpdater;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;

class TranslationUpdaterTest extends TestCase
{
    private FileTranslationUpdater $updater;

    public function setUp(): void
    {
        $adapter = new InMemoryFilesystemAdapter();

        $filesystem    = new Filesystem($adapter);
        $this->updater = new FileTranslationUpdater($filesystem, 'tests/translations');
    }

    public function testInvalidKey(): void
    {
        $this->expectException(InvalidKeyException::class);
        $this->updater->updateTranslation('key', 'value', 'nl');
    }

    public function testUpdatingTranslation(): void
    {
        $result = $this->updater->updateTranslation('app.key', 'value', 'nl');
        $this->assertTrue($result);
    }

    public function testDeletingTranslation(): void
    {
        $result = $this->updater->removeTranslation('app.holidays.january.newyear', 'nl');
        $this->assertTrue($result);
    }
}
