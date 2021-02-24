<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ShipmentsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::unprepared(file_get_contents(__DIR__ . './../dumps/shipments.sql'));

//    $excel = IOFactory::load(__DIR__ . '/assets/plan_postavki.xlsx');
//
//    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
//    $data = $excel->getActiveSheet()->rangeToArray('C4:' . $maxCell['column'] . $maxCell['row']);
//
//    $plan_postavki = [];
//
//    foreach ($data as $key => $row) {
//      if ($row[5] != 0) {
//        $period = DB::table('periods')->where('name', $row[1])->get()->all()[0];
//        $article = DB::table('payment_balance_articles')->where('code', $row[0])->get()->all()[0];
//        $dkre = DB::table('dkres')->where('region', $row[3])->get()->all()[0];
//
//        if ($row[2] == 'ПЕР') { // Перевозки
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '1';
//          $arr['payment_balance_article_id'] = $article->id;
//          $arr['source_id'] = $row[4] == 'ЦЗ/РЗ' ? 1 : 2;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['version_id'] = 1;
//          $arr['count'] = $row[5];
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($plan_postavki, $arr);
//        }
//
//        if ($row[2] == 'ПВД') { // ПВД
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '2';
//          $arr['payment_balance_article_id'] = $article->id;
//          $arr['source_id'] = $row[4] == 'ЦЗ/РЗ' ? 1 : 2;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['version_id'] = 1;
//          $arr['count'] = $row[5];
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($plan_postavki, $arr);
//        }
//
//        if ($row[2] == 'ИНВ') { // КВ
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '3';
//          $arr['payment_balance_article_id'] = $article->id;
//          $arr['source_id'] = $row[4] == 'ЦЗ/РЗ' ? 1 : 2;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['version_id'] = 1;
//          $arr['count'] = $row[5];
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($plan_postavki, $arr);
//        }
//
//        if ($row[2] == 'ПРО') { // Прочие
//          $arr = [];
//          $arr['period_id'] = $period->id;
//          $arr['activity_type_id'] = '4';
//          $arr['payment_balance_article_id'] = $article->id;
//          $arr['source_id'] = $row[4] == 'ЦЗ/РЗ' ? 1 : 2;
//          $arr['dkre_id'] = $dkre->id;
//          $arr['version_id'] = 1;
//          $arr['count'] = $row[5];
//          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
//          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
//
//          array_push($plan_postavki, $arr);
//        }
//      }
//    }
//
//    DB::table('shipments')->insert($plan_postavki);
  }
}
