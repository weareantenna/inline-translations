<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations;

use Antenna\InlineTranslations\Exceptions\InvalidTranslationFileException;
use Antenna\InlineTranslations\Exceptions\TranslationUpdateException;
use Antenna\InlineTranslations\Models\TranslationKey;
use League\Flysystem\Filesystem;

use function array_filter;
use function array_replace_recursive;
use function array_reverse;
use function count;
use function file_exists;
use function is_array;
use function is_string;
use function var_export;

interface TranslationUpdater
{
    public function updateTranslation(string $key, ?string $value, string $language): bool;
    public function removeTranslation(string $key, string $language): bool;
}
