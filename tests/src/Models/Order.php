<?php

namespace Dvarilek\FilamentTableSelect\Tests\src\Models;

use Dvarilek\FilamentTableSelect\Tests\Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}