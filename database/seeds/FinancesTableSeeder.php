<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;

class FinancesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {

    function getCell($cell) {
      $excel = IOFactory::load(__DIR__ . '/assets/f22.xlsx');
      $cell = (double)$excel->getActiveSheet()->getCell($cell)->getValue();

      return $cell;
    }

    $f22 = [];
    $supplies = ['22' => '1', '23' => '2', '24' => '3', '26' => '1', '27' => '2', '28' => '3', '30' => '1', '31' => '2', '32' => '3', '34' => '1', '35' => '2', '36' => '3'];
    $articles = [1 => 'Q', 2 => 'R', 3 => 'S', 4 => 'T', 13 => 'W', 19 => 'X', 20 => 'Y', 21 => 'Z', 22 => 'AA', 23 => 'AB', 25 => 'AC'];

    foreach ($supplies as $row => $supply) {
      foreach ($articles as $key => $col) {
        if ($row == '22' || $row == '23' || $row == '24') { // ПЕРЕВОЗКИ
          $arr = [];
          $arr['period_id'] = getCell('H17');
          $arr['activity_type_id'] = '1';
          $arr['source_id'] = $supply;
          $arr['payment_balance_article_id'] = $key;
          $arr['version_id'] = 2;
          $arr['count'] = round(getCell($col . $row), 3);
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($f22, $arr);
        }

        if ($row == '26' || $row == '27' || $row == '28') { // ПВД
          $arr = [];
          $arr['period_id'] = getCell('H17');
          $arr['activity_type_id'] = '2';
          $arr['source_id'] = $supply;
          $arr['version_id'] = 2;
          $arr['payment_balance_article_id'] = $key;
          $arr['count'] = round(getCell($col . $row), 3);
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($f22, $arr);
        }

        if ($row == '30' || $row == '31' || $row == '32') { // КВ
          $arr = [];
          $arr['period_id'] = getCell('H17');
          $arr['activity_type_id'] = '3';
          $arr['source_id'] = $supply;
          $arr['version_id'] = 2;
          $arr['payment_balance_article_id'] = $key;
          $arr['count'] = round(getCell($col . $row), 3);
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($f22, $arr);
        }

        if ($row == '34' || $row == '35' || $row == '36') { // ПРОЧИЕ
          $arr = [];
          $arr['period_id'] = getCell('H17');
          $arr['activity_type_id'] = '4';
          $arr['source_id'] = $supply;
          $arr['version_id'] = 2;
          $arr['payment_balance_article_id'] = $key;
          $arr['count'] = round(getCell($col . $row), 3);
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($f22, $arr);
        }
      }
    }

    DB::table('finances')->insert($f22);
  }
}
