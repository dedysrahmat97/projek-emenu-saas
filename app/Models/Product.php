<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'product_category_id',
        'image',
        'name',
        'description',
        'price',
        'rating',
        'is_popular',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (auth()->user()->role == 'store') {
                $model->user_id = auth()->user()->id;
            }

        });

        static::updating(function ($model) {

            if (auth()->user()->role == 'store') {
                $model->user_id = auth()->user()->id;
            }
            
        });
    }

    protected $casts =[
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function productIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }
}