<?php

namespace App\Models;

use App\Traits\ActiveStore;
use App\Traits\WhoDidIt;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $quantity
 * @property int $order_item_status_id
 * @property int $inventory_id
 * @property int $received_by_id
 * @property int $receiving_comment
 * @property int $shipping_slip
 * @property int $location
 * @property string $expected_delivery_date
 * @property string $actual_delivery_date
 * @property int $tracking_number
 * @property float $purchase_cost
 * @property string $store_id
 * @property string $method
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TransferRequest|null $active_transfer
 * @property-read Inventory|null $inventory
 * @property-read Store|null $store
 * @property-read \Illuminate\Database\Eloquent\Collection|TransferRequest[] $transfer_requests
 * @property-read int|null $transfer_requests_count
 * @method static Builder|OrderItem newModelQuery()
 * @method static Builder|OrderItem newQuery()
 * @method static Builder|OrderItem query()
 * @method static Builder|OrderItem whereActualDeliveryDate($value)
 * @method static Builder|OrderItem whereCreatedAt($value)
 * @method static Builder|OrderItem whereCreatedById($value)
 * @method static Builder|OrderItem whereExpectedDeliveryDate($value)
 * @method static Builder|OrderItem whereId($value)
 * @method static Builder|OrderItem whereInventoryId($value)
 * @method static Builder|OrderItem whereLocation($value)
 * @method static Builder|OrderItem whereMethod($value)
 * @method static Builder|OrderItem whereOrderItemStatusId($value)
 * @method static Builder|OrderItem wherePurchaseCost($value)
 * @method static Builder|OrderItem whereQuantity($value)
 * @method static Builder|OrderItem whereReceivedById($value)
 * @method static Builder|OrderItem whereReceivingComment($value)
 * @method static Builder|OrderItem whereShippingSlip($value)
 * @method static Builder|OrderItem whereStoreId($value)
 * @method static Builder|OrderItem whereTrackingNumber($value)
 * @method static Builder|OrderItem whereUpdatedAt($value)
 * @method static Builder|OrderItem whereUpdatedById($value)
 * @mixin Eloquent
 * @property-read User|null $created_by
 * @property-read User|null $updated_by
 * @method static Builder|OrderItem activeStore(?string $storeId = null)
 */
class OrderItem extends Model
{
    use HasFactory;
    use WhoDidIt;
    use ActiveStore;

    public const PENDING = 1;
    public const DENIED = 2;
    public const ACCEPTED = 3;
    public const ORDERED = 4;
    public const RECEIVED = 5;
    public const TRANSFER_REQUESTED = 6;

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::saved(function (OrderItem $orderItem) {
            if ($orderItem->actual_delivery_date && $orderItem->isDirty('actual_delivery_date')) {
                InventoryTransaction::record($orderItem->inventory_id, $orderItem->quantity, 2);
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function received_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transfer_requests(): HasMany
    {
        return $this->hasMany(TransferRequest::class);
    }

    public function active_transfer(): HasOne
    {
        return $this->hasOne(TransferRequest::class)
            ->whereIn('transfer_status_id', [
                TransferRequest::TRANSFER_REQUEST,
                TransferRequest::TRANSFER_APPROVED_FOR_PICKUP,
                TransferRequest::TRANSFER_APPROVED_FOR_DELIVERY,
            ]);
    }

    public static function itemCount(array $order_item_status_ids, string $user_id, $store_id = null): int
    {
        $query = OrderItem::whereIn('order_items.order_item_status_id', $order_item_status_ids);

        if ($store_id) {
            $query->where(['order_items.store_id' => $store_id]);
        } else {
            $query->whereHas('store.users', function (Builder $query) use ($user_id) {
                return $query->where(['users.id' => $user_id]);
            });
        }


        return $query->count();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|Collection $data
     * @param string $store_id
     * @param string $method
     * @param bool $save
     * @return \Illuminate\Database\Eloquent\Collection|Collection|void
     */
    public static function order(\Illuminate\Database\Eloquent\Collection|Collection $data, string $store_id, string $method, bool $save = true)
    {
        $data = $data->filter(fn($i) => $i['order'] > 0);
        if (!$data->count()) { return; }

        $orderItems = $data->map( function ($orderItem) use ($store_id, $method) {
            return new OrderItem([
                'store_id' => $store_id,
                'method' => $method,
                'quantity' => $orderItem['order'],
                'order_item_status_id' => self::PENDING,
                'inventory_id' => $orderItem['id'],
            ]);
        });

        if ($save) {
            $orderItems->each->save();
        }

        return $orderItems;
    }
}
