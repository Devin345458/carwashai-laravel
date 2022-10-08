<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\WhoDidIt;
use Auth;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Inventory
 *
 * @property int $id
 * @property int $item_id
 * @property string $store_id
 * @property int $supplier_id
 * @property string $cost
 * @property int $current_stock
 * @property int $initial_stock
 * @property int $desired_stock
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read File|null $file
 * @property-read Collection|InventoryTransaction[] $inventory_transactions
 * @property-read int|null $inventory_transactions_count
 * @property-read Item|null $item
 * @property-read Collection|OrderItem[] $order_items
 * @property-read int|null $order_items_count
 * @property-read Store|null $store
 * @property-read Supplier|null $supplier
 * @method static Builder|Inventory newModelQuery()
 * @method static Builder|Inventory newQuery()
 * @method static Builder|Inventory query()
 * @method static Builder|Inventory whereCost($value)
 * @method static Builder|Inventory whereCreatedAt($value)
 * @method static Builder|Inventory whereCreatedById($value)
 * @method static Builder|Inventory whereCurrentStock($value)
 * @method static Builder|Inventory whereDesiredStock($value)
 * @method static Builder|Inventory whereId($value)
 * @method static Builder|Inventory whereInitialStock($value)
 * @method static Builder|Inventory whereItemId($value)
 * @method static Builder|Inventory whereStoreId($value)
 * @method static Builder|Inventory whereSupplierId($value)
 * @method static Builder|Inventory whereUpdatedAt($value)
 * @method static Builder|Inventory whereUpdatedById($value)
 * @mixin Eloquent
 * @method static Builder|Inventory activeStore(?string $storeId = null)
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class Inventory extends Model
{
    use HasFactory;
    use ActiveStore;
    use WhoDidIt;

    protected $fillable = [
        'item_id',
        'store_id',
        'supplier_id',
        'cost',
        'current_stock',
        'initial_stock',
        'desired_stock',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Inventory $inventory) {
            $inventory->initial_stock = $inventory->current_stock;
        });

        static::created(function (Inventory $inventory) {
            $inventory->inventory_transactions()->create([
                'transaction_action_id' => 1,
                'quantity' => $inventory->current_stock,
                'difference' => $inventory->current_stock,
            ]);
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function inventory_transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @throws Exception
     */
    public static function use(Item $item, int $quantity, string $store_id, int $transaction_action_id) {
        $inventory = static::firstOrCreate(['item_id' => $item->id, 'store_id' => $store_id], [
            'item_id' => $item->id,
            'store_id' => $store_id,
            'current_stock' => 0,
            'supplier_id' => 0,
            'cost' => 0,
            'desired_stock' => 0,
        ]);
        InventoryTransaction::record($inventory->id, $quantity, $transaction_action_id);
    }
}
