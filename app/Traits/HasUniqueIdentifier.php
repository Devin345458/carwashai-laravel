<?php

namespace App\Traits;


use Illuminate\Database\Eloquent\Model;

use Ramsey\Uuid\Uuid;


trait HasUniqueIdentifier
{
    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }
    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    public static function bootHasUniqueIdentifier(): void
    {
        static::creating(function (Model $model) {
            $model->setKeyType('string');
            $model->setIncrementing(false);
            $model->setAttribute($model->getKeyName(), Uuid::uuid4()->toString());
        });
    }

}
