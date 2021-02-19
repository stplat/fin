<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
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

      $model->permissions()->attach('1'); // Главная
      $model->permissions()->attach('7'); // Мой склад
      $model->permissions()->attach('8'); // Невостребованные
      $model->permissions()->attach('9'); // Заявка
      $model->permissions()->attach('10'); // Отдать материал
      $model->permissions()->attach('11'); // Забрать материал
      $model->permissions()->attach('12'); // Вывести материал

      if ($role['slug'] == 'admin') {
        $model->permissions()->attach('2'); //
        $model->permissions()->attach('3'); //
        $model->permissions()->attach('4'); //
        $model->permissions()->attach('5'); //
        $model->permissions()->attach('6'); //
      }
    }
  }
}
