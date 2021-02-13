<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::table('permissions')->insert([
      [
        'name' => 'Панель управления', // 1.
        'slug' => 'view-',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Мониторинг', // 2.
        'slug' => 'view-monitoring',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Библиотека', // 3.
        'slug' => 'view-library',
        'created_at' => now(),
        'updated_at' => now()
      ],
    ]);
  }
}
