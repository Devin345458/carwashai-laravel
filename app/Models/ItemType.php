<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ItemType
 *
 * @property int $id
 * @property string $name
 * @property int $company_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Collection|Item[] $items
 * @property-read int|null $items_count
 * @method static Builder|ItemType newModelQuery()
 * @method static Builder|ItemType newQuery()
 * @method static Builder|ItemType query()
 * @method static Builder|ItemType whereCompanyId($value)
 * @method static Builder|ItemType whereCreatedAt($value)
 * @method static Builder|ItemType whereCreatedById($value)
 * @method static Builder|ItemType whereId($value)
 * @method static Builder|ItemType whereName($value)
 * @method static Builder|ItemType whereUpdatedAt($value)
 * @method static Builder|ItemType whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class ItemType extends Model
{
    use HasFactory;
    use WhoDidIt;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }


}
