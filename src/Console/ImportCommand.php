<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Console;

use Antenna\InlineTranslations\TranslationFetcher;
use Antenna\InlineTranslations\TranslationUpdater;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use function array_keys;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function preg_match_all;

class ImportCommand extends Command
{
    /** @var string */
    protected $signature = 'translations:import {--save : Save changes in translation files }';

    /** @var string */
    protected $description = 'Import translations from your source code files (this command defaults to a dry-run)';

    private Finder $finder;

    /** @var string[] */
    private array $translationFunctions;

    private TranslationUpdater $translationUpdater;

    /** @var string[] */
    private array $existingKeys;

    private string $mainLocale;

    /**
     * @param string[] $translationFunctions
     */
    public function __construct(
        Finder $finder,
        array $translationFunctions,
        TranslationFetcher $translationFetcher,
        TranslationUpdater $translationUpdater,
        string $mainLocale
    ) {
        $this->finder               = $finder;
        $this->translationFunctions = $translationFunctions;
        $this->translationUpdater   = $translationUpdater;
        $this->existingKeys         = array_keys($translationFetcher->fetchByLanguage($mainLocale));
        $this->mainLocale           = $mainLocale;
        parent::__construct();
    }

    public function handle() : void
    {
        $this->info('Scanning files for new translation keys' . "\n");
        // See https://regex101.com/r/WEJqdL/6
        $groupPattern = '[^\w|>]' . '(' . implode('|', $this->translationFunctions) . ')' .
            '\(' . "[\'\"]" . '(' . '[a-zA-Z0-9_-]+' . "([.](?! )[^\1)]+)+" . ')' . "[\'\"]" . '[\),]';

        $groupKeys = [];
        foreach ($this->finder->files() as $file) {
            if (! preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                continue;
            }

            // Get all matches
            foreach ($matches[2] as $key) {
                $groupKeys[] = $key;
            }
        }

        $newKeys = $this->importNewKeys(array_unique($groupKeys));

        $this->info("\n" . count($newKeys) . ' new keys ' . ($this->option('save') ? 'saved' : 'found'));
    }

    /**
     * @param string[] $allKeys
     * @return string[]
     */
    private function importNewKeys(array $allKeys) : array
    {
        $newKeys = [];

        foreach ($allKeys as $key) {
            if (in_array($key, $this->existingKeys, true)) {
                continue;
            }

            $newKeys[] = $key;
            if ($this->option('save')) {
                $this->translationUpdater->updateTranslation($key, null, $this->mainLocale);
                $this->line('[SAVED] ' . $key);
            } else {
                $this->line('[NEW] ' . $key);
            }
        }

        return $newKeys;
    }
}
