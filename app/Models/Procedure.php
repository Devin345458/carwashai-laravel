<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Procedure
 *
 * @property int $id
 * @property string $name
 * @property string $store_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Procedure newModelQuery()
 * @method static Builder|Procedure newQuery()
 * @method static Builder|Procedure query()
 * @method static Builder|Procedure whereCreatedAt($value)
 * @method static Builder|Procedure whereId($value)
 * @method static Builder|Procedure whereName($value)
 * @method static Builder|Procedure whereStoreId($value)
 * @method static Builder|Procedure whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|ProcedureDay[] $days
 * @property-read int|null $days_count
 * @property-read Collection|ProcedureStep[] $steps
 * @property-read int|null $steps_count
 */
class Procedure extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (Procedure $procedure) {
            $procedure->steps()->delete();
            $procedure->days()->delete();
        });

    }

    public function days(): HasMany
    {
        return $this->hasMany(ProcedureDay::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProcedureStep::class);
    }
}
