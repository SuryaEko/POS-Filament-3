<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
 * @property User $user
 * @property Collection|OrderDetail[] $order_details
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';

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

	public function user()
	{
		return $this->belongsTo(User::class, 'customer_id');
	}

	public function order_details()
	{
		return $this->hasMany(OrderDetail::class);
	}
}
