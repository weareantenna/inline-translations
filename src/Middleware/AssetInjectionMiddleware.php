<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use function is_int;
use function is_string;
use function preg_replace;
use function route;
use function strripos;
use function substr;

class AssetInjectionMiddleware
{
    public function handle(Request $request, Closure $next) : BaseResponse
    {
        $response = $next($request);
        if ($response instanceof Response) {
            $response = $this->injectTranslator($response);
        }

        return $response;
    }

    private function injectTranslator(Response $response) : Response
    {
        $content = $response->getContent();
        if (! is_string($content)) {
            return $response;
        }

        $pos = strripos($content, '</head>');
        if (! is_int($pos)) {
            return $response;
        }

        $cssRoute = preg_replace('/https?:/', '', route('inline-translations.assets.css'));
        $jsRoute  = preg_replace('/https?:/', '', route('inline-translations.assets.js'));

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}'>\n";
        $html .= "<script type='text/javascript' src='{$jsRoute}'></script>\n";

        $content = substr($content, 0, $pos) . $html . substr($content, $pos);

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }
}
