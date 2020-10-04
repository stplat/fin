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
    $finances = Finance::select(DB::raw("
      payment_balance_articles.name as article, 
      payment_balance_articles.id as article_id, 
      activity_types.code as activity, 
      sources.id as source, 
      ROUND(SUM(finances.count), 3) as total
    "))
      ->join('activity_types', 'activity_types.id', '=', 'finances.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.id', '=', 'finances.payment_balance_article_id')
      ->join('sources', 'sources.id', '=', 'finances.source_id')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->orderBy('finances.payment_balance_article_id')
      ->groupBy('finances.payment_balance_article_id', 'finances.activity_type_id', 'sources.id')
      ->get();

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
