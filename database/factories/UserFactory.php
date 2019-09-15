<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
//
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'email_verified_at' => now(),
        'password' => bcrypt('secret'),
        'remember_token' => Str::random(10),
    ];
});


$factory->define(\App\Models\Publisher::class, function (Faker $faker) {
    return [
        'username' => $faker->name,
        'display_name' => $faker->name,
        'mobile' => $faker->phoneNumber,
        'city' => $faker->streetName,
        'email' => $faker->email,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});


$factory->define(\App\Models\Store::class, function (Faker $faker) {
    return [
        'store_name' => $faker->name,
        'mobile' => $faker->phoneNumber,
        'city' => $faker->streetName,
        'email' => $faker->email,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'store_type_id'=>'1',
    ];
});
