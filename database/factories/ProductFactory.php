<?php

/** @var Factory $factory */

use App\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Product::class, function (Faker $faker) {
    $faker->addProvider(new Commerce($faker));
    $name = $faker->productName();
    return [
        'sku'=>$faker->sku($name, '-'),
        'name'=>$name,
        'quantity'=>$faker->randomNumber(),
        'price'=>$faker->randomFloat(2,9.99, 999999.99),
        'description'=>$faker->paragraph,
        'image'=>$faker->image('public/storage/images',400,300, null, false) ,
    ];
});
