<?php

namespace Antenna\InlineTranslations\Handlers\Db;

use Antenna\InlineTranslations\TranslationFetcher;

class DbTranslationFetcher implements TranslationFetcher
{
    public function fetchAll(): array
    {
        $translations = TranslationKey::with(['languageLine'])->get();

        $result = [];
        foreach ($translations as $translation) {
            foreach ($translation->languageLines as $languageLine) {
                if (!array_key_exists($languageLine->language, $result)) {
                    $result[$languageLine->language] = [];
                }

                $result[$languageLine->language][$translation->key] = $languageLine->translation;
            }
        }

        return $result;
    }

    public function fetchAllGroupedByKeys(): array
    {
        $translations = TranslationKey::with(['languageLine'])->get();

        $result = [];
        foreach ($translations as $translation) {
            foreach ($translation->languageLines as $languageLine) {
                if (!array_key_exists($translation->key, $result)) {
                    $result[$translation->key] = [];
                }

                $result[$translation->key][$languageLine->language] = $languageLine->translation;
            }
        }

        return $result;
    }

    public function fetchByLanguage(string $language): array
    {
        $translations = TranslationKey::with(['languageLine'])->get();
        $result = [];
        foreach ($translations as $translation) {
            foreach ($translation->languageLines as $languageLine) {
                if ($languageLine->language === $language) {
                    $result[$translation->key] = $languageLine->translation;
                }
            }
        }

        return $result;
    }
}
