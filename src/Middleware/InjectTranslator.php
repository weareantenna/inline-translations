<?php

namespace Antenna\InlineTranslations\Middleware;

use Closure;
use Illuminate\Http\Response;

class InjectTranslator
{
    protected $cssFiles = [];

    public function handle($request, Closure $next)
    {
        try {
            /** @var Response $response */
            $response = $next($request);
        } catch (Exception $e) {
            $response = $this->handleException($request, $e);
        } catch (Error $error) {
            $e = new FatalThrowableError($error);
            $response = $this->handleException($request, $e);
        }

        return $response;
    }
}
