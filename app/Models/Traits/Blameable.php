<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property int|null $deleted_by_id
 * @property-read User|null $createdBy
 * @property-read User|null $updatedBy
 * @property-read User|null $deletedBy
 */
trait Blameable
{
    /**
     * Boot the Blameable trait for a model.
     */
    public static function bootBlameable(): void
    {
        static::creating(function (Model $model): void {
            if (! Auth::check()) {
                return;
            }

            /** @var int $userId */
            $userId = Auth::id();

            // @phpstan-ignore-next-line property.notFound (Property exists on models using this trait)
            $model->created_by_id ??= $userId;
            // @phpstan-ignore-next-line property.notFound (Property exists on models using this trait)
            $model->updated_by_id ??= $userId;
        });

        static::updating(function (Model $model): void {
            if (! Auth::check()) {
                return;
            }

            /** @var int $userId */
            $userId = Auth::id();

            // @phpstan-ignore-next-line property.notFound (Property exists on models using this trait)
            $model->updated_by_id = $userId;
        });

        static::deleting(function (Model $model): void {
            if (! Auth::check()) {
                return;
            }

            /** @var int $userId */
            $userId = Auth::id();

            // @phpstan-ignore-next-line property.notFound (Property exists on models using this trait)
            $model->deleted_by_id = $userId;
        });

        $persistDeletedBy = function (Model $model): void {
            if (
                ! method_exists($model, 'trashed')
                || ! $model->trashed()
                || ! $model->isDirty('deleted_by_id')
            ) {
                return;
            }

            $model->saveQuietly();
        };

        static::deleted($persistDeletedBy);
        static::forceDeleted($persistDeletedBy);
    }

    /**
     * The user who created the model.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * The user who last updated the model.
     *
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * The user who deleted the model.
     *
     * @return BelongsTo<User, $this>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }
}
