<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Involvement;
use App\Models\Version;

class InvolvementService
{

  /**
   * Getting budget by period and version.
   *
   * @param $periods array
   * @param $version integer
   * @param $regions array
   *
   * @return \Illuminate\Support\Collection
   */
  public function getInvolvement($periods, $version, $regions = null)
  {
    $dkres = $regions ?: Dkre::get()->pluck('id');
    $involvement = Involvement::select(DB::raw("
    dkres.region, 
    payment_balance_articles.code as article, 
    activity_types.name as activity,
    ROUND(SUM(involvements.involve_by_prepayment_last_year), 3) as involve_last,
    ROUND(SUM(involvements.involve_by_prepayment_current_year), 3) as involve_current,
    ROUND(SUM(involvements.involve_by_turnover), 3) as involve_turnover,
    ROUND(SUM(involvements.prepayment), 3) as prepayment
    "))
      ->join('dkres', 'dkres.id', '=', 'involvements.dkre_id')
      ->join('activity_types', 'activity_types.id', '=', 'involvements.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.id', '=', 'involvements.payment_balance_article_id')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->whereIn('dkre_id', $dkres)
      ->orderBy('involvements.dkre_id')
      ->orderBy('involvements.activity_type_id')
      ->groupBy('involvements.dkre_id', 'involvements.payment_balance_article_id', 'involvements.activity_type_id')
      ->get();

    return $involvement->groupBy('region')->map(function ($item, $key) {
      return collect([
        'dkre' => $key,
        'activity' => $item->groupBy('activity')->map(function ($item, $key) {
          return collect([
            'name' => $key,
            'involve_last' => round($item->sum('involve_last'), 3),
            'involve_current' => round($item->sum('involve_current'), 3),
            'involve_turnover' => round($item->sum('involve_turnover'), 3),
            'prepayment' => round($item->sum('prepayment'), 3),
          ]);
        })->values(),
        'total' => collect([
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment' => round($item->sum('prepayment'), 3),
        ])
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => $involvement->groupBy('activity')->map(function ($item, $key) {
        return collect([
          'name' => $key,
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment' => round($item->sum('prepayment'), 3),
        ]);
      })->values(),
      'total' => collect([
        'involve_last' => round($involvement->sum('involve_last'), 3),
        'involve_current' => round($involvement->sum('involve_current'), 3),
        'involve_turnover' => round($involvement->sum('involve_turnover'), 3),
        'prepayment' => round($involvement->sum('prepayment'), 3),
      ])
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
}
