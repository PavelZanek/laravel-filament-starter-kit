<?php

declare(strict_types=1);

use App\Http\Middleware\ApplyTenantScopes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

it('allows requests to pass through the ApplyTenantScopes middleware', function (): void {
    Route::middleware(ApplyTenantScopes::class)->get('/test', function (): Illuminate\Contracts\Routing\ResponseFactory|Response {
        return response('Test successful');
    });

    $request = Request::create('/test', 'GET');

    $response = Route::dispatch($request);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($response->getContent())->toBe('Test successful');
});
