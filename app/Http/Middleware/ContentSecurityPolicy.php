<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Development uchun yumshoq CSP
        if (app()->environment('local', 'development')) {
            $csp = "default-src 'self'; ";
            $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com 'wasm-unsafe-eval'; ";
            $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ";
            $csp .= "img-src 'self' data: https: blob:; ";
            $csp .= "font-src 'self' https://fonts.gstatic.com; ";
            $csp .= "connect-src 'self' http://127.0.0.1:8000 https://* ws: wss:; ";
            $csp .= "media-src 'self' blob: data:; ";
            $csp .= "object-src 'none'; ";
            $csp .= "frame-ancestors 'self'; ";
            $csp .= "base-uri 'self'; ";
            $csp .= "form-action 'self';";
            $csp .= "worker-src 'self' blob:;";
            $csp .= "child-src 'self' blob:;";
            $csp .= "frame-src 'self' blob:;";
        } else {
            // Production uchun qattiqroq CSP, lekin AJAX uchun yumshatirilgan
            $nonce = base64_encode(random_bytes(16));
            session(['csp_nonce' => $nonce]);
            
            $csp = "default-src 'self'; ";
            $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com; ";
            $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ";
            $csp .= "img-src 'self' data: https: blob:; ";
            $csp .= "font-src 'self' https://fonts.gstatic.com; ";
            $csp .= "connect-src 'self' *; ";
            $csp .= "media-src 'self' blob:; ";
            $csp .= "object-src 'none'; ";
            $csp .= "frame-ancestors 'self'; ";
            $csp .= "base-uri 'self'; ";
            $csp .= "form-action 'self';";
        }
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        return $response;
    }
}