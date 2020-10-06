<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Finance;
use App\Models\Version;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FinanceService
{

  /**
   * Getting budget by period and version.
   *
   * @param $periods array
   * @param $version integer
   *
   * @return \Illuminate\Support\Collection
   */
  public function getFinances($periods, $version)
  {
    $periodSql = implode(',', $periods);
    $finances = collect(DB::select("
      SELECT payment_balance_articles.sub_general as article_id, 
      payment_balance_articles.sub_general_name as article, 
      activity_types.code as activity, 
      finances.source_id as source, 
      SUM(finances.count) as total
      FROM `finances`
      LEFT JOIN 
      (SELECT payment_balance_articles.sub_general, payment_balance_articles.sub_general_name 
       FROM payment_balance_articles 
       GROUP BY payment_balance_articles.sub_general, payment_balance_articles.sub_general_name) payment_balance_articles
      ON payment_balance_articles.sub_general = finances.payment_balance_article_sub_general
      LEFT JOIN `activity_types` ON activity_types.id = finances.activity_type_id
      WHERE version_id=$version AND period_id in ($periodSql)
      GROUP BY payment_balance_articles.sub_general, payment_balance_articles.sub_general_name, finances.activity_type_id, finances.source_id
    "));

    return $finances->groupBy('article')->map(function ($item, $key) {
      return collect([
        'name' => $key,
        'activity' => $item->groupBy('activity')->map(function ($item, $key) {
          return $item->groupBy('source')->map(function ($item, $key) {
            return round($item->sum('total'), 3);
          });
        })
      ]);
    })->put('', collect([
      'name' => 'ИТОГО',
      'activity' => $finances->groupBy('activity')->map(function ($item, $key) {
        return $item->groupBy('source')->map(function ($item, $key) {
          return round($item->sum('total'), 3);
        });
      })
    ]))->values();
  }

  /**
   * Получаем список ДКРЭ
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getDkres()
  {
    return Dkre::all()->unique('name')->values();
  }

  /**
   * Получаем список Регионов
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getRegions()
  {
    return Dkre::all();
  }

  /**
   * Получаем список Периодов
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getPeriods($type = null)
  {
    return !$type ? Period::all() : Period::where('type', $type)->get();
  }

  /**
   * Получаем список Версий
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getVersions()
  {
    return Version::all();
  }

  /**
   * Парсим загруженный файл
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getUploadFile($file, $version)
  {
    $excel = IOFactory::load($file);

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    return ParserInObjectExcelHelper($data, $version);
  }
}
