<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Budget;
use App\Models\Version;

class BudgetService
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
  public function getBudget($periods, $version, $regions = null)
  {
    $dkres = $regions ?: Dkre::get()->pluck('id');
    $budget = Budget::select(DB::raw("
    dkres.region, payment_balance_articles.general as article, activity_types.name as activity, ROUND(SUM(budgets.count), 3) as total
    "))
      ->join('dkres', 'dkres.id', '=', 'budgets.dkre_id')
      ->join('activity_types', 'activity_types.id', '=', 'budgets.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.general', '=', 'budgets.payment_balance_article_general')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->whereIn('dkre_id', $dkres)
      ->orderBy('budgets.dkre_id')
      ->orderBy('budgets.activity_type_id')
      ->groupBy('budgets.dkre_id', 'budgets.payment_balance_article_general', 'budgets.activity_type_id')
      ->get();

    return $budget->groupBy('region')->map(function ($item, $key) {
      return collect([
        'dkre' => $key,
        'activity' => $item->groupBy('activity')->map(function ($item, $key) {
          return collect([
            'name' => $key,
            'article' => $item->groupBy('article')->map(function ($item, $key) {
              return round($item->sum('total'), 3);
            })
          ]);
        })->values(),
        'total' => $item->groupBy('article')->map(function ($item) {
          return round($item->sum('total'), 3);
        })
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => $budget->groupBy('activity')->map(function ($item, $key) {
        return collect([
          'name' => $key,
          'article' => $item->groupBy('article')->map(function ($item, $key) {
            return round($item->sum('total'), 3);
          })
        ]);
      })->values(),
      'total' => $budget->groupBy('article')->map(function ($item, $key) {
        return round($item->sum('total'), 3);
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
}
