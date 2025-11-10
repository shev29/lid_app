<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::check()) {
        //     return redirect(Session::get('url.intended', route('home')));
        // }

        if (Auth::check()) {
            session()->put('last_activity', now()->timestamp);
            $redirectUri = $request->query('redirect_uri');

            if ($redirectUri) {
                $decodedUrl = urldecode($redirectUri);
                if (filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
                    return redirect($decodedUrl);
                }
                else {
                    return redirect(route('home'));
                }
            }

            return redirect(route('home'));
        }

        return $next($request);
    }
}
