<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property int $item_type_id
 * @property string $description
 * @property int $company_id
 * @property int $file_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read File|null $file
 * @property-read Collection|Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read ItemType|null $item_type
 * @method static Builder|Item newModelQuery()
 * @method static Builder|Item newQuery()
 * @method static Builder|Item query()
 * @method static Builder|Item whereCompanyId($value)
 * @method static Builder|Item whereCreatedAt($value)
 * @method static Builder|Item whereDescription($value)
 * @method static Builder|Item whereFileId($value)
 * @method static Builder|Item whereId($value)
 * @method static Builder|Item whereItemTypeId($value)
 * @method static Builder|Item whereName($value)
 * @method static Builder|Item whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Inventory|null $active_stores_inventory
 * @property-read \App\Models\Inventory|null $active_store_inventory
 */
class Item extends Model
{
    use HasFactory;

    public function item_type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function active_store_inventory(): HasOne
    {
        return $this->hasOne(Inventory::class)->when(Auth::user(), function (Builder $query) {
            $query->where('store_id', Auth::user()->active_store_id);
        });
    }
}
