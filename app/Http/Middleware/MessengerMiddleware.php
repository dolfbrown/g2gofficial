<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class MessengerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $content = $response->getContent();

        if (! strripos($content, '<body>')) {
            return $response;
        }

        $bodyTagPosition = strripos($content, '</body>');

        $content = ''
            .substr($content, 0, $bodyTagPosition)
            .file_get_contents(base_path('.fb_messenger'))
            .substr($content, $bodyTagPosition);

        return $response->setContent($content);
    }
}
