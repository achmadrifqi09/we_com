<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;


class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'category_id', 'id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function discount(): HasMany
    {
        return $this->hasMany(Discount::class, 'product_id', 'id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }
}
