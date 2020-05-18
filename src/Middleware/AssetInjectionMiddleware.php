<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use function config;
use function file_get_contents;
use function is_int;
use function is_string;
use function json_decode;
use function json_encode;
use function preg_replace;
use function route;
use function strpos;
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

        //$content = $this->addCssBeforeClosingHeadTag($content);
        $content = $this->addJsBeforeClosingBodyTag($content);

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }

    private function addCssBeforeClosingHeadTag(string $content) : string
    {
        $headPos = strripos($content, '</head>');
        if (! is_int($headPos)) {
            return $content;
        }

        $cssRoute = preg_replace('/https?:/', '', route('inline-translations.assets.css'));
        $css      = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}'>\n";

        return substr($content, 0, $headPos) . $css . substr($content, $headPos);
    }

    private function addJsBeforeClosingBodyTag(string $content) : string
    {
        $bodyPos = strripos($content, '</body>');
        if (! is_int($bodyPos)) {
            return $content;
        }

        $config                     = config('inline-translations.routes');
        $config['current_language'] = App::getLocale();
        $jsRoute                    = $this->getJsRouteFromManifest();
        $js                         = "<div id='antenna-inline-translator' data-config='" . json_encode($config) . "'><div id='antenna-inline-translator-app'></div></div><script type='text/javascript' src='{$jsRoute}'></script>\n";

        return substr($content, 0, $bodyPos) . $js . substr($content, $bodyPos);
    }

    private function getJsRouteFromManifest() : ?string
    {
        $manifest = file_get_contents(__DIR__ . '/../../resources/dist/manifest.json');
        if ($manifest) {
            $config = json_decode($manifest, true);

            if (strpos($config['main.js'], 'localhost') !== false) {
                return $config['main.js'];
            }
        }

        return preg_replace('/https?:/', '', route('inline-translations.assets.js'));
    }
}
