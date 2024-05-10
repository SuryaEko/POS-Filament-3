<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 *
 * @package App\Models
 */
class Customer extends Model
{
    protected $table = 'customers';

    protected $casts = [
        'user_id' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)
            ->whereHas('roles', function ($query) {
                $query->where('roles.name', 'customer');
            });
    }
}
