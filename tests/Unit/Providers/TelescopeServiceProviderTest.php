<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

it('allows user with allowed email to view Telescope', function (): void {
    Config::set('project.telescope.allowed_email', 'allowed@example.com');

    $user = User::factory()->create(['email' => 'allowed@example.com']);

    expect(Gate::allows('viewTelescope', $user))->toBeTrue();
})->skip();

it('denies user with disallowed email to view Telescope', function (): void {
    Config::set('project.telescope.allowed_email', 'allowed@example.com');

    $user = User::factory()->create(['email' => 'disallowed@example.com']);

    expect(Gate::allows('viewTelescope', $user))->toBeFalse();
})->skip();
