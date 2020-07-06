<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $excel = IOFactory::load(__DIR__ . '/budget.xlsx');

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A5:' . $maxCell['column'] . $maxCell['row']);

    $budget = [];

    foreach ($data as $key => $row) {
      if ($row[5] != 0) {
        $period = DB::table('period')->where('name', $row[2])->get()->all()[0];
        $article = DB::table('statya_pb')->where('code', $row[1])->get()->all()[0];
        $dkre = DB::table('dkre')->where('zavod', $row[3])->get()->all()[0];

        if ($row[4] == 'ПЕР') { // ПЕРЕВОЗКИ
          $arr = [];
          $arr['period_id'] = $period->id;
          $arr['vid_deyatelnosti_id'] = '1';
          $arr['statya_pb_id'] = $article->id;
          $arr['dkre_id'] = $dkre->id;
          $arr['sum'] = $row[5];
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($budget, $arr);
        }

        if ($row[4] == 'ПВД') { // ПВД
          $arr = [];
          $arr['period_id'] = $period->id;
          $arr['vid_deyatelnosti_id'] = '2';
          $arr['statya_pb_id'] = $article->id;
          $arr['dkre_id'] = $dkre->id;
          $arr['sum'] = $row[5];
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($budget, $arr);
        }

        if ($row[4] == 'ИНВ') { // КВ
          $arr = [];
          $arr['period_id'] = $period->id;
          $arr['vid_deyatelnosti_id'] = '3';
          $arr['statya_pb_id'] = $article->id;
          $arr['dkre_id'] = $dkre->id;
          $arr['sum'] = $row[5];
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($budget, $arr);
        }

        if ($row[4] == 'ПРО') { // Прочие
          $arr = [];
          $arr['period_id'] = $period->id;
          $arr['vid_deyatelnosti_id'] = '4';
          $arr['statya_pb_id'] = $article->id;
          $arr['dkre_id'] = $dkre->id;
          $arr['sum'] = $row[5];
          $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
          $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

          array_push($budget, $arr);
        }
      }
    }

    DB::table('budget')->insert($budget);
  }
}