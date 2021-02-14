<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $excel = IOFactory::load(app()->basePath('database/seeds/assets/users.xlsx'));
    $excel->setActiveSheetIndex(0);
    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    ExcelParseHelper($data)->each(function ($item) {
      $user = new User();
      $user->name = $item['name'];
      $user->email = $item['email'];
      $user->password = Hash::make($item['password']);
      $user->dkre_id = $item['dkre_id'];
      $user->remember_token = Str::random(10);
      $user->created_at = now();
      $user->updated_at = now();
      $user->save();
    });
  }
}
