<?php

namespace App\Http\Middleware;

use Closure;

class SellerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSeller()) {
            abort(403, 'Bạn chưa có gian hàng đang hoạt động');
        }

        return $next($request);
    }
}
