<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Storage;

/**
 * Class Product
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $thumbnail
 * @property string|null $description
 * @property float $price
 * @property int $stock
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|OrderDetail[] $order_details
 * @property Collection|ProductCategory[] $product_categories
 * @property Collection|Category[] $categories
 *
 * @package App\Models
 */
class Product extends Model
{
    protected $table = 'products';

    protected $casts = [
        'price' => 'float'
    ];

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'price',
        'stock',
    ];

    protected static function boot()
    {
        parent::boot();

        /** @var Model $model */
        static::updating(function (self $model) {
            /* delete old thumbnail */
            if ($model->isDirty('thumbnail') && ($model->getOriginal('thumbnail') !== null)) {
                Storage::disk('local')->delete($model->getOriginal('thumbnail'));
            }
        });
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withPivot([
                'created_at',
                'updated_at'
            ]);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }
}
