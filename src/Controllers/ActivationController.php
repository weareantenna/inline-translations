<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

class ActivationController extends BaseController
{
    public function enable() : RedirectResponse
    {
        return (new RedirectResponse(config('inline-translations.routes.redirect_url')))
            ->withCookie('inline-translations-active', true);
    }

    public function disable() : RedirectResponse
    {
        return (new RedirectResponse(config('inline-translations.routes.redirect_url')))
            ->withCookie(Cookie::forget('inline-translations-active'));
    }
}
