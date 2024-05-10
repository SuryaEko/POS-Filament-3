<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Order
 *
 * @property int $id
 * @property int $customer_id
 * @property int $cashier_id
 * @property string $invoice_number
 * @property float $total
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $customer
 * @property User|null $cashier
 * @property Collection|OrderDetail[] $order_details
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Order extends Model
{
    protected $table = 'orders';

    const INVOICE_PREFIX = 'INV-';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESS = 'processing';
    const STATUS_COMPLETE = 'completed';
    const STATUS_CANCEL = 'canceled';

    public static array $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PROCESS => 'Processing',
        self::STATUS_COMPLETE => 'Completed',
        self::STATUS_CANCEL => 'Canceled'
    ];

    protected $casts = [
        'customer_id' => 'int',
        'cashier_id' => 'int',
        'total' => 'float'
    ];

    protected $fillable = [
        'customer_id',
        'cashier_id',
        'invoice_number',
        'total',
        'status'
    ];

    public static function getInvoiceNumber()
    {
        $latest = self::latest()->first();

        if (!$latest) {
            return self::INVOICE_PREFIX . '0001';
        }

        return self::INVOICE_PREFIX . sprintf('%04d', $latest->id + 1);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id')
            ->whereHas('roles', function ($query) {
                $query->where('roles.name', 'cashier');
            });
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_details')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }
}
