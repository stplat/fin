<?php

namespace App\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;

class TestController extends Controller {
  public function index() {

    $excel = IOFactory::load($_SERVER['DOCUMENT_ROOT'] . '/database/seeds/plan_postavki.xlsx');

    function getCell($excel, $cell) {
      $cell = $excel->getActiveSheet()->getCell($cell);
      return $cell;
    }

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('C4:' . $maxCell['column'] . $maxCell['row']);

    $plan_postavki = [];

    foreach ($data as $row) {

      $period = DB::table('period')->where('name', $row[1])->get()->all()[0];
      $article = DB::table('article')->where('code', $row[0])->get()->all()[0];
      $dkre = DB::table('dkre')->where('zavod', $row[3])->get()->all()[0];

      if ($row[2] == 'ПЕР') { // Перевозки
        $arr = [];
        $arr['period_id'] = $period->period_id;
        $arr['activity_id'] = '3';
        $arr['article_id'] = $article->article_id;

        $row[2] == 'ЦЗ/РЗ' ?: $arr['supply_id'] = '1';
        $row[2] == 'СЗ' ?: $arr['supply_id'] = '2';

        $arr['dkre_id'] = $dkre->dkre_id;
        $arr['sum'] = $row[5];
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        array_push($plan_postavki, $arr);
      }

      if ($row[2] == 'ПВД') { // ПВД
        $arr = [];
        $arr['period_id'] = $period->period_id;
        $arr['activity_id'] = '3';
        $arr['article_id'] = $article->article_id;

        $row[2] == 'ЦЗ/РЗ' ?: $arr['supply_id'] = '1';
        $row[2] == 'СЗ' ?: $arr['supply_id'] = '2';

        $arr['dkre_id'] = $dkre->dkre_id;
        $arr['sum'] = $row[5];
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        array_push($plan_postavki, $arr);
      }

      if ($row[2] == 'ИНВ') { // Инвестиции
        $arr = [];
        $arr['period_id'] = $period->period_id;
        $arr['activity_id'] = '3';
        $arr['article_id'] = $article->article_id;

        $row[2] == 'ЦЗ/РЗ' ?: $arr['supply_id'] = '1';
        $row[2] == 'СЗ' ?: $arr['supply_id'] = '2';

        $arr['dkre_id'] = $dkre->dkre_id;
        $arr['sum'] = $row[5];
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        array_push($plan_postavki, $arr);
      }

      if ($row[2] == 'ПРО') { // Прочие
        $arr = [];
        $arr['period_id'] = $period->period_id;
        $arr['activity_id'] = '3';
        $arr['article_id'] = $article->article_id;

        $row[2] == 'ЦЗ/РЗ' ?: $arr['supply_id'] = '1';
        $row[2] == 'СЗ' ?: $arr['supply_id'] = '2';

        $arr['dkre_id'] = $dkre->dkre_id;
        $arr['sum'] = $row[5];
        $arr['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $arr['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        array_push($plan_postavki, $arr);
      }

    }

    dd($plan_postavki);
    //DB::table('budget')->insert($plan_postavki);
  }
}
