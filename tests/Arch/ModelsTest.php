<?php

declare(strict_types=1);

arch('models')
    ->expect(getModels())
    ->toHaveMethod('casts')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toOnlyBeUsedIn([
        'App\Concerns',
        'App\Console',
        'App\EventActions',
        'App\Filament',
        'App\Http',
        'App\Jobs',
        'App\Livewire',
        'App\Observers',
        'App\Mail',
        'App\Models',
        'App\Notifications',
        'App\Policies',
        'App\Providers',
        'App\Queries',
        'App\Rules',
        'App\Services',
        'Database\Factories',
    ])->ignoring([
        App\Models\User::class,
        App\Models\Role::class,
    ]);

arch('ensure factories', function (): void {
    expect($models = getModels())->toHaveCount(4);

    foreach ($models as $model) {
        /* @var \Illuminate\Database\Eloquent\Factories\HasFactory $model */
        expect($model::factory())
            ->toBeInstanceOf(Illuminate\Database\Eloquent\Factories\Factory::class);
    }
});

arch('ensure datetime casts', function (): void {
    expect($models = getModels())->toHaveCount(4);

    foreach ($models as $model) {
        /* @var \Illuminate\Database\Eloquent\Factories\HasFactory $model */
        $instance = $model::factory()->create();

        $dates = collect($instance->getAttributes())
            ->filter(fn ($_, $key): bool => str_ends_with($key, '_at'))
            ->reject(fn ($_, $key): bool => in_array($key, ['created_at', 'updated_at']));

        foreach ($dates as $key => $value) {
            expect($instance->getCasts())->toHaveKey(
                $key,
                'datetime',
                sprintf(
                    'The %s cast on the %s model is not a datetime cast.',
                    $key,
                    $model,
                ),
            );
        }
    }
});

/**
 * Get all models in the app/Models directory.
 *
 * @return array<int, class-string<Illuminate\Database\Eloquent\Model>>
 */
function getModels(): array
{
    /** @var array<int, string> $models */
    $models = glob(__DIR__.'/../../app/Models/*.php');

    return collect($models)
        ->map(function ($file): string {
            return 'App\Models\\'.basename($file, '.php');
        })->toArray();
}
