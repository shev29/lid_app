<?php

namespace Modules\ProcurementPurchasing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProcurementPurchasing\Database\Factories\FreightPriceRequestModelFactory;

class FreightPriceRequestModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): FreightPriceRequestModelFactory
    // {
    //     // return FreightPriceRequestModelFactory::new();
    // }
}
