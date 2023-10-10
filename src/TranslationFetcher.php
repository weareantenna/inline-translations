<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

interface TranslationFetcher
{
    /** @return array<string, array<string>> */
    public function fetchAll(): array;

    /** @return array<int|string,array<string, string>> */
    public function fetchAllGroupedByKeys(): array;

    /** @return string[] */
    public function fetchByLanguage(string $language): array;
}
