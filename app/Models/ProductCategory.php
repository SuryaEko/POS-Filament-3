<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategory
 * 
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Category $category
 * @property Product $product
 *
 * @package App\Models
 */
class ProductCategory extends Model
{
	protected $table = 'product_categories';

	protected $casts = [
		'product_id' => 'int',
		'category_id' => 'int'
	];

	protected $fillable = [
		'product_id',
		'category_id'
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
