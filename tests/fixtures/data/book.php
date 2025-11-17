<?php

$faker = Faker\Factory::create();
$faker->seed(5678);
$books = [];
$now = time();

for ($i = 1; $i <= 100; $i++) {
    $books["book{$i}"] = [
        'id' => $i,
        'title' => $faker->unique()->sentence(3),
        'author' => $faker->name,
        'description' => $faker->paragraph,
        'year' => $faker->numberBetween(1950, (int) date('Y')),
        'created_by' => $faker->numberBetween(1, 10),
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

return $books;
