<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // if (!Auth::check()) {
        //     if ($request->header('Accept') === 'application/json') {
        //         if($request->header('Referer')){
        //             Session::put('url.intended', $request->header('Referer'));
        //         }

        //         return response()->json([
        //             'message' => 'Unauthenticated',
        //             'redirect' => route('login')
        //         ], 401); // 401 Unauthorized
        //     }
        //     else {
        //         Session::put('url.intended', $request->fullUrl());
        //         return redirect()->route('login');
        //     }
        // }

        if (!Auth::check()) {
            $currentUrl = $request->fullUrl();
            $encodedUrl = urlencode($currentUrl);

            if ($request->header('Accept') === 'application/json') {
                if($request->header('Referer')){
                    $encodedUrl = urlencode($request->header('Referer'));
                }

                return response()->json([
                    'message' => 'Unauthenticated',
                    'redirect_uri' => route('login', ['redirect_uri' => $encodedUrl])
                ], 401); // 401 Unauthorized
            }
            else {
                return redirect()->route('login', ['redirect_uri' => $encodedUrl]);
            }
        }
        else{
            $user = Auth::user();
            $employeeId = $user->employee_id;
            $employeeActive = DB::table('vw_master_employee_active')
                                    ->where('employee_id', $employeeId)
                                    ->where('is_active', '1')
                                    ->first();
            if(!$employeeActive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $request->session()->flush();

                return redirect()->route('login');
            }
            else {
                session()->put('last_activity', now()->timestamp);
                return $next($request);
            }
        }

        // if (!Auth::check()) {
        //     // Pastikan URL yang disimpan adalah URL halaman yang valid
        //     if ($request->isMethod('get') && !$request->ajax()) {
        //         Session::put('url.intended', $request->fullUrl());
        //     }
        //     return redirect()->route('login');
        // }

        // return $next($request);
    }
}
