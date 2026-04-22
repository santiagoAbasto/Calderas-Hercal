<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            HomeSeeder::class,
            NosotrosSeeder::class,
            ServicesPageSeeder::class,
            EquipmentsPageSeeder::class,
            ProjectsPageSeeder::class,
            ClientesPageSeeder::class,
            ContactoPageSeeder::class,
            PresupuestoPageSeeder::class,
        ]);
    }
}
