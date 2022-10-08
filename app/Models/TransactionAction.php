<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransactionAction
 *
 * @property int $id
 * @property string $name
 * @property int $operation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|InventoryTransaction[] $inventory_transactions
 * @property-read int|null $inventory_transactions_count
 * @method static Builder|TransactionAction newModelQuery()
 * @method static Builder|TransactionAction newQuery()
 * @method static Builder|TransactionAction query()
 * @method static Builder|TransactionAction whereCreatedAt($value)
 * @method static Builder|TransactionAction whereId($value)
 * @method static Builder|TransactionAction whereName($value)
 * @method static Builder|TransactionAction whereOperation($value)
 * @method static Builder|TransactionAction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TransactionAction extends Model
{
    use HasFactory;

    public const OPERATION_ADD = 0;
    public const OPERATION_REMOVE = 1;
    public const OPERATION_SET = 2;

    public const INITIAL_STOCK = 1;
    public const RECEIVED_STOCK = 2;
    public const STOCK_USED = 3;
    public const USER_IN_REPAIR = 4;
    public const USED_IN_MAINTENANCE = 5;
    public const INVENTORY_CONDUCTED = 6;


    public function inventory_transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

}
