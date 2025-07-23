<?php

namespace Dvarilek\FilamentTableSelect\Tests\database\factories;

use Dvarilek\FilamentTableSelect\Tests\Models\Order;
use Dvarilek\FilamentTableSelect\Tests\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'order_id' => Order::factory(),
        ];
    }

    public function withOrder(Order $order): self
    {
        return $this->state([
            'order_id' => $order->getKey(),
        ]);
    }
}
