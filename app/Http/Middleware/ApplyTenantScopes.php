<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyTenantScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        Category::addGlobalScope(
//            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
//        );
//        Tag::addGlobalScope(
//            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
//        );
//        Product::addGlobalScope(
//            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
//        );

        return $next($request);
    }
}