<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Database\Factories\CategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $category
 * @property string $description
 * @property int $company_id
 * @property string $type
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Collection|Equipment[] $equipment
 * @property-read int|null $equipment_count
 * @method static CategoryFactory factory(...$parameters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCategory($value)
 * @method static Builder|Category whereCompanyId($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereCreatedById($value)
 * @method static Builder|Category whereDescription($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereType($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class Category extends Model
{
    use HasFactory;
    use WhoDidIt;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }

}
