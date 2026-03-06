<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@biblioteca.test'],
            ['name' => 'Admin Biblioteca', 'password' => 'password', 'role' => User::ROLE_ADMIN]
        );

        User::updateOrCreate(
            ['email' => 'editor@biblioteca.test'],
            ['name' => 'Cidadao Demo', 'password' => 'password', 'role' => User::ROLE_CIDADAO]
        );

        $this->call(BibliotecaSeeder::class);
    }
}
