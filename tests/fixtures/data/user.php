<?php

$faker = Faker\Factory::create();
$faker->seed(1234);
$users = [];
$passwordHash = password_hash('secret123', PASSWORD_BCRYPT);
$now = time();

for ($i = 1; $i <= 10; $i++) {
    $username = $faker->userName . $i;
    $users["user{$i}"] = [
        'id' => $i,
        'username' => $username,
        'email' => $faker->unique()->safeEmail,
        'password_hash' => $passwordHash,
        'auth_key' => $faker->sha1,
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

return $users;
