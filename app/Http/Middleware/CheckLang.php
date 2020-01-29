<?php

namespace App\Http\Middleware;

use Closure;

class CheckLang
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
        if(!isset($request["lang"]))
            $request['lang'] = 'ar';

        \App::setLocale($request["lang"]);

        return $next($request);
    }


}
