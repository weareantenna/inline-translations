<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

use function assert;
use function config;
use function is_string;

class ActivationController extends BaseController
{
    public function enable(): RedirectResponse
    {
        $uri = config('inline-translations.routes.redirect_url');
        assert(is_string($uri));

        return (new RedirectResponse($uri))
            ->withCookie('inline-translations-active', true);
    }

    public function disable(): RedirectResponse
    {
        $uri = config('inline-translations.routes.redirect_url');
        assert(is_string($uri));

        return (new RedirectResponse($uri))
            ->withCookie(Cookie::forget('inline-translations-active'));
    }
}
