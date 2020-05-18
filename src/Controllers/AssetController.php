<?php

declare(strict_types=1);

namespace Antenna\InlineTranslations\Controllers;

use DateTime;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use function file_get_contents;

class AssetController extends BaseController
{
    /** @return string[] */
    private function getStylesheets() : array
    {
        return [];
    }

    /** @return string[] */
    private function getJavascript() : array
    {
        return ['dist/translations.bundle.js'];
    }

    public function js() : Response
    {
        $response = new Response(
            $this->dumpAssetsToString($this->getJavascript()),
            200,
            ['Content-Type' => 'text/javascript']
        );

        return $this->cacheResponse($response);
    }

    public function css() : Response
    {
        $response = new Response(
            $this->dumpAssetsToString($this->getStylesheets()),
            200,
            ['Content-Type' => 'text/css']
        );

        return $this->cacheResponse($response);
    }

    protected function cacheResponse(Response $response) : Response
    {
        $response->setMaxAge(31536000);
        $response->setExpires(new DateTime('+1 year'));

        return $response;
    }

    /** @param string[] $assets */
    private function dumpAssetsToString(array $assets) : string
    {
        $assetString = '';
        foreach ($assets as $asset) {
            $assetString .= file_get_contents(__DIR__ . '/../../resources/' . $asset) . "\n";
        }

        return $assetString;
    }
}
