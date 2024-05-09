<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ProductCategory[] $product_categories
 *
 * @package App\Models
 */
class Category extends Model
{
	protected $table = 'categories';

	protected $fillable = [
		'name',
		'slug',
		'description'
	];

	public function product_categories()
	{
		return $this->hasMany(ProductCategory::class);
	}
}
