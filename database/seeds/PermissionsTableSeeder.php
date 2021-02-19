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
        'slug' => 'view-index',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Бюджетные параметры', // 2.
        'slug' => 'view-budget.index',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Вовлечение', // 3.
        'slug' => 'view-involvement.index',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'План поставок', // 4.
        'slug' => 'view-shipment.index',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Форма №22', // 5.
        'slug' => 'view-finance.index',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Денежная заявка', // 6.
        'slug' => 'view-application.index',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Мой склад', // 7.
        'slug' => 'view-material.warehouse',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Невостребованные', // 8.
        'slug' => 'view-material.unused',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Заявки', // 9.
        'slug' => 'view-material.orders',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Отдать материал', // 10.
        'slug' => 'view-material.push',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Забрать материал', // 11.
        'slug' => 'view-material.pull',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Вывести материалы', // 12.
        'slug' => 'view-material.all',
        'created_at' => now(),
        'updated_at' => now()
      ],
    ]);
  }
}
