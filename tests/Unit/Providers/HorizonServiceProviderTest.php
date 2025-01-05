<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

it('allows user with allowed email to view Horizon', function (): void {
    Config::set('horizon.allowed_email', 'allowed@example.com');

    $user = User::factory()->create(['email' => 'allowed@example.com']);

    expect(Gate::allows('viewHorizon', $user))->toBeTrue();
})->skip();

it('denies user with disallowed email to view Horizon', function (): void {
    Config::set('horizon.allowed_email', 'allowed@example.com');

    $user = User::factory()->create(['email' => 'disallowed@example.com']);

    expect(Gate::allows('viewHorizon', $user))->toBeFalse();
})->skip();
