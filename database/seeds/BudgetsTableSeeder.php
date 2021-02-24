<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::unprepared(file_get_contents(__DIR__ . './../dumps/budgets.sql'));

//    $excel = IOFactory::load(__DIR__ . '/assets/budget.xlsx');
//
//    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
//    $data = $excel->getActiveSheet()->rangeToArray('A5:' . $maxCell['column'] . $maxCell['row']);
//
//    $budget = [];
//
//    foreach ($data as $key => $row) {
//      if ($row[5] != 0) {
//        $period = DB::table('periods')->where('name', $row[2])->get()->all()[0];
//        $article = DB::table('payment_balance_articles')->where('code', $row[1])->get()->all()[0];
//        $dkre = DB::table('dkres')->where('region', $row[3])->get()->all()[0];
//        $version = DB::table('versions')->where('name', $row[6])->first();
//
//        if ($row[4] == 'ПЕР') { // ПЕРЕВОЗКИ
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '1';
//          $arr['payment_balance_article_general'] = $article->general;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['count'] = $row[5];
//          $arr['version_id'] = $version->id;
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($budget, $arr);
//        }
//
//        if ($row[4] == 'ПВД') { // ПВД
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '2';
//          $arr['payment_balance_article_general'] = $article->general;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['count'] = $row[5];
//          $arr['version_id'] = $version->id;
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($budget, $arr);
//        }
//
//        if ($row[4] == 'ИНВ') { // КВ
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '3';
//          $arr['payment_balance_article_general'] = $article->general;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['count'] = $row[5];
//          $arr['version_id'] = $version->id;
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($budget, $arr);
//        }
//
//        if ($row[4] == 'ПРО') { // Прочие
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '4';
//          $arr['payment_balance_article_general'] = $article->general;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['count'] = $row[5];
//          $arr['version_id'] = $version->id;
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($budget, $arr);
//        }
//      }
//    }
//
//    DB::table('budgets')->insert($budget);
  }
}
