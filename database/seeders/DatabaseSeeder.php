<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@catalog.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
            ]
        );

        $categories = [
            ['name' => 'Электроника', 'description' => 'Гаджеты и техника'],
            ['name' => 'Одежда', 'description' => 'Мужская и женская'],
            ['name' => 'Книги', 'description' => 'Художественная и техническая литература'],
            ['name' => 'Дом и сад', 'description' => 'Товары для дома'],
            ['name' => 'Спорт', 'description' => 'Инвентарь и аксессуары'],
        ];

        foreach ($categories as $data) {
            $category = Category::query()->firstOrCreate(
                ['name' => $data['name']],
                ['description' => $data['description']]
            );

            Product::factory()
                ->count(random_int(4, 10))
                ->create(['category_id' => $category->id]);
        }
    }
}
