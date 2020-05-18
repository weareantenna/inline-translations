<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Test;

use Antenna\InlineTranslations\TranslationFetcher;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class TranslationFetcherTest extends TestCase
{
    private TranslationFetcher $fetcher;

    public function setUp(): void
    {
        $adapter       = new Local(__DIR__ . '/translations');
        $filesystem    = new Filesystem($adapter);
        $this->fetcher = new TranslationFetcher($filesystem);
    }

    /** @test */
    public function it_can_read_translation_keys_per_language(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('Monday', $translations['en']['app.days.monday']);
        $this->assertEquals('Maandag', $translations['nl']['app.days.monday']);
    }

    /** @test */
    public function it_can_read_nested_keys_per_language(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('Christmas', $translations['en']['app.holidays.december.christmas']);
        $this->assertEquals('Kerstmis', $translations['nl']['app.holidays.december.christmas']);
    }

    /** @test */
    public function it_can_read_vendor_keys(): void
    {
        $translations = $this->fetcher->fetchAll();

        $this->assertEquals('vendor string', $translations['en']['PackageName::vendor.key']);
    }

    /** @test */
    public function it_can_fetch_single_language(): void
    {
        $translations = $this->fetcher->fetchByLanguage('en');

        $this->assertEquals('Monday', $translations['app.days.monday']);
    }

    /** @test */
    public function it_can_fetch_single_unexisting_language(): void
    {
        $translations = $this->fetcher->fetchByLanguage('fr');
        $this->assertEmpty($translations);
    }

    /** @test */
    public function it_can_fetch_translations_grouped_by_key(): void
    {
        $translations = $this->fetcher->fetchAllGroupedByKeys();

        $this->assertEquals([
            'nl' => 'Dinsdag',
            'en' => 'Tuesday'
        ], $translations['app.days.tuesday']);
    }
}
