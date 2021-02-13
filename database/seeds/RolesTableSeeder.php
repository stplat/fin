<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;

class RolesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $roles = array(
      ['name' => 'Администратор', 'slug' => 'admin'],
      ['name' => 'Аппарат управления', 'slug' => 'center'],
      ['name' => 'Структурное подразделение', 'slug' => 'region'],
    );

    foreach ($roles as $role) {
      $model = new Role();
      $model->name = $role['name'];
      $model->slug = $role['slug'];
      $model->created_at = now();
      $model->updated_at = now();
      $model->save();
//      $model->permissions()->attach('1'); // Главная
//      $model->permissions()->attach('2'); // Мониторинг
//      $model->permissions()->attach('3'); // Библиотека
//      $model->permissions()->attach('4'); // Управление
//      $model->permissions()->attach('5'); // Посты контроля
//      $model->permissions()->attach('6'); // События
//      $model->permissions()->attach('7'); // Правонарушения
//      $model->permissions()->attach('8'); // Белые номера
//      $model->permissions()->attach('9'); // Статистика
//      $model->permissions()->attach('10'); // Пользователи
//      $model->permissions()->attach('11'); // Настройки
    }
  }
}
