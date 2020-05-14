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

        $content = $this->addCssBeforeClosingHeadTag($content);
        $content = $this->addJsBeforeClosingBodyTag($content);

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }

    private function addCssBeforeClosingHeadTag(string $content)
    {
        $headPos = strripos($content, '</head>');
        if (! is_int($headPos)) {
            return $content;
        }

        $cssRoute = preg_replace('/https?:/', '', route('inline-translations.assets.css'));
        $css  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}'>\n";
        return substr($content, 0, $headPos) . $css . substr($content, $headPos);
    }

    private function addJsBeforeClosingBodyTag(string $content)
    {
        $bodyPos = strripos($content, '</body>');
        if (! is_int($bodyPos)) {
            return $content;
        }

        $jsRoute  = preg_replace('/https?:/', '', route('inline-translations.assets.js'));
        $js = "<div id='antenna-inline-translator'></div><script type='text/javascript' src='{$jsRoute}'></script>\n";
        return substr($content, 0, $bodyPos) . $js . substr($content, $bodyPos);
    }
}
