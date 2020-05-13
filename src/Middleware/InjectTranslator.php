<?php

namespace Antenna\InlineTranslations\Middleware;

use Closure;

class InjectTranslator
{
    protected $cssFiles = [];
    
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
