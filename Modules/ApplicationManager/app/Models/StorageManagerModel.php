<?php

namespace Modules\ApplicationManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ApplicationManager\Database\Factories\StorageManagerFactory;

class StorageManagerModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): StorageManagerFactory
    // {
    //     // return StorageManagerFactory::new();
    // }
}
