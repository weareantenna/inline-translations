<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Test;

use Antenna\InlineTranslations\TranslationFetcher;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;

class TranslationFetcherTest extends TestCase
{
    private TranslationFetcher $fetcher;

    public function setUp(): void
    {
        $adapter       = new LocalFilesystemAdapter(__DIR__ . '/translations');
        $filesystem    = new Filesystem($adapter);
        $this->fetcher = new TranslationFetcher($filesystem, __DIR__ . '/translations/');
    }

    public function testReadAllTranslationsPerLanguage(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('Monday', $translations['en']['app.days.monday']);
        $this->assertEquals('Maandag', $translations['nl']['app.days.monday']);
    }

    public function testReadNestedKeysPerLanguage(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('Christmas', $translations['en']['app.holidays.december.christmas']);
        $this->assertEquals('Kerstmis', $translations['nl']['app.holidays.december.christmas']);
    }

    public function testReadVendorKeys(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('vendor string', $translations['en']['PackageName::vendor.key']);
    }

    public function testFetchSingleLanguage(): void
    {
        $translations = $this->fetcher->fetchByLanguage('en');

        $this->assertEquals('Monday', $translations['app.days.monday']);
    }

    public function testFetchUnexistingLanguage(): void
    {
        $translations = $this->fetcher->fetchByLanguage('fr');
        $this->assertEmpty($translations);
    }

    public function testFetchedAllTranslationsGroupedByKey(): void
    {
        $translations = $this->fetcher->fetchAllGroupedByKeys();

        $this->assertEquals([
            'nl' => 'Dinsdag',
            'en' => 'Tuesday',
        ], $translations['app.days.tuesday']);
    }
}
