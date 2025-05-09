<?php

namespace Dvarilek\FilamentTableSelect\Tests\src\Models;

use Dvarilek\FilamentTableSelect\Tests\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}