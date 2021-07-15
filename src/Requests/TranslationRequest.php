<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
{
    /** @return array<string,string> */
    public function rules() : array
    {
        return [
            'key' => 'required',
            'language' => 'required',
        ];
    }
}
