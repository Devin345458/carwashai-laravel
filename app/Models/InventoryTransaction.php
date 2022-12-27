<?php

namespace App\Models;

use App\Traits\WhoDidIt;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InventoryTransaction
 *
 * @property int $id
 * @property int $quantity
 * @property int $difference
 * @property int $transaction_action_id
 * @property int $inventory_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Inventory|null $inventory
 * @property-read TransactionAction|null $transaction_action
 * @method static Builder|InventoryTransaction newModelQuery()
 * @method static Builder|InventoryTransaction newQuery()
 * @method static Builder|InventoryTransaction query()
 * @method static Builder|InventoryTransaction whereCreatedAt($value)
 * @method static Builder|InventoryTransaction whereCreatedById($value)
 * @method static Builder|InventoryTransaction whereDifference($value)
 * @method static Builder|InventoryTransaction whereId($value)
 * @method static Builder|InventoryTransaction whereInventoryId($value)
 * @method static Builder|InventoryTransaction whereQuantity($value)
 * @method static Builder|InventoryTransaction whereTransactionActionId($value)
 * @method static Builder|InventoryTransaction whereUpdatedAt($value)
 * @method static Builder|InventoryTransaction whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read \App\Models\User|null $created_by
 * @property-read \App\Models\User|null $updated_by
 */
class InventoryTransaction extends Model
{
    use HasFactory;
    use WhoDidIt;

    protected static function boot()
    {
        parent::boot();
        static::saved(function (InventoryTransaction $inventoryTransaction) {
            if ($inventoryTransaction->transaction_action_id !== TransactionAction::INITIAL_STOCK) {
                $inventoryTransaction->inventory->current_stock = $inventoryTransaction->inventory->current_stock + $inventoryTransaction->difference;
                $inventoryTransaction->inventory->save();
            }
        });
    }

    public function transaction_action(): BelongsTo
    {
        return $this->belongsTo(TransactionAction::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public static function record(int $inventory_id, int $quantity, int $action_id, bool $save = true): InventoryTransaction
    {
        $action = TransactionAction::find($action_id);

        switch ($action->operation) {
            case TransactionAction::OPERATION_SET:
                $inventory = Inventory::find($inventory_id);
                $difference = $quantity - $inventory->current_stock;
                break;
            case TransactionAction::OPERATION_ADD:
                $difference = $quantity;
                break;
            case TransactionAction::OPERATION_REMOVE:
                $difference = $quantity * -1;
                break;
            default:
                throw new Exception('Invalid Transaction Action Operation');
        }

        $record = new InventoryTransaction([
            'inventory_id' => $inventory_id,
            'transaction_action_id' => $action_id,
            'quantity' => $quantity,
            'difference' => $difference,
        ]);

        if (!$save) {
            return $record;
        }

        $record->save();

        return $record;
    }
}
