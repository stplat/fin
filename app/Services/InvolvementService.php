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
    dkres.id as dkre_id,
    payment_balance_articles.general as article_id,
    activity_types.id as activity_id,
    dkres.region, 
    activity_types.name as activity,
    ROUND(involvements.involve_by_prepayment_last_year, 3) as involve_last,
    ROUND(involvements.involve_by_prepayment_current_year, 3) as involve_current,
    ROUND(involvements.involve_by_turnover, 3) as involve_turnover,
    ROUND(involvements.prepayment_current_year, 3) as prepayment_current,
    ROUND(involvements.prepayment_next_year, 3) as prepayment_next
    "))
      ->join('dkres', 'dkres.id', '=', 'involvements.dkre_id')
      ->join('activity_types', 'activity_types.id', '=', 'involvements.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.general', '=', 'involvements.payment_balance_article_general')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->whereIn('dkre_id', $dkres)
      ->orderBy('involvements.dkre_id')
      ->orderBy('involvements.activity_type_id')
      ->groupBy('involvements.dkre_id', 'involvements.payment_balance_article_general', 'involvements.activity_type_id',
        'involvements.involve_by_prepayment_last_year', 'involvements.involve_by_prepayment_current_year', 'involvements.involve_by_turnover',
        'involvements.prepayment_current_year', 'involvements.prepayment_next_year')
      ->get();

    return $involvement->groupBy('region')->map(function ($item, $key) use ($version, $periods) {
      return collect([
        'dkre' => $key,
        'dkre_id' => $item[0]->dkre_id,
        'article_id' => $item[0]->article_id,
        'activity' => $item->groupBy('activity')->map(function ($item, $key) {
          return collect([
            'activity_id' => $item[0]->activity_id,
            'name' => $key,
            'involve_last' => round($item->sum('involve_last'), 3),
            'involve_current' => round($item->sum('involve_current'), 3),
            'involve_turnover' => round($item->sum('involve_turnover'), 3),
            'prepayment_current' => round($item->sum('prepayment_current'), 3),
            'prepayment_next' => round($item->sum('prepayment_next'), 3),
          ]);
        })->values(),
        'total' => collect([
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
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
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
        ]);
      })->values(),
      'total' => collect([
        'involve_last' => round($involvement->sum('involve_last'), 3),
        'involve_current' => round($involvement->sum('involve_current'), 3),
        'involve_turnover' => round($involvement->sum('involve_turnover'), 3),
        'prepayment_current' => round($involvement->sum('prepayment_current'), 3),
        'prepayment_next' => round($involvement->sum('prepayment_next'), 3),
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
   * @param $type integer

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
