<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'phone_number',
        'table_number',
        'payment_method',
        'total_price',
        'status',
    ];

    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            
            static::creating(function ($model) {
    
                if (Auth::user()->role == 'store') {
                    $model->user_id = auth()->user()->id;
                }
    
            });
    
            static::updating(function ($model) {
    
                if (auth()->user()->role == 'store') {
                    $model->user_id = auth()->user()->id;
                }
                
            });
        }

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}