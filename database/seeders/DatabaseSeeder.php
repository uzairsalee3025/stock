<?php

namespace Database\Seeders;

use App\Models\IngredientCategory;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default users — one per role. Password for all: "password".
        $users = [
            ['name' => 'Administrator', 'email' => 'admin@clinic.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'Reception Staff', 'email' => 'staff@clinic.test', 'role' => User::ROLE_STAFF],
            ['name' => 'Inventory Manager', 'email' => 'inventory@clinic.test', 'role' => User::ROLE_INVENTORY],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'role' => $u['role'], 'password' => Hash::make('password'), 'is_active' => true],
            );
        }

        // A few starter categories so the dropdowns aren't empty on first run.
        foreach (['Herbs', 'Oils', 'Powders', 'Liquids'] as $name) {
            IngredientCategory::firstOrCreate(['name' => $name]);
        }
        foreach (['Finished Goods', 'Bottles', 'Packaged Products'] as $name) {
            ProductCategory::firstOrCreate(['name' => $name]);
        }
    }
}
