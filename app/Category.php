<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function subcategories()
    {
    	return $this->hasMany(Subcategory::class, 'category_id');
    }

		public function products()
		{
			return $this->hasMany(Product::class, 'subcategory_id');
		}

		public function products_by_category()
		{
			return DB::table('categories')->join('products', 'category.id', '=', 'products.category_id')->get();
		}


}
