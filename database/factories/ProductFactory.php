<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding Product.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the Product's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(20),
            'description' => $this->faker->realText(),
            'price' => $this->faker->randomFloat(2,1,1000),
            'product_image' => $this->faker->unique()->imageUrl(329,364)
        ];
    }
}
