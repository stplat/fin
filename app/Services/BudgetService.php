<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Version;
use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;

class BudgetService
{

  /**
   * Getting budget by period and version.
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getBudget($period = [3, 4], $version = 2)
  {
    $budget = Budget::select(DB::raw("
    dkres.region, payment_balance_articles.code as article, activity_types.name as activity, ROUND(SUM(budgets.count), 3) as total
    "))
      ->join('dkres', 'dkres.id', '=', 'budgets.dkre_id')
      ->join('activity_types', 'activity_types.id', '=', 'budgets.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.id', '=', 'budgets.payment_balance_article_id')
      ->whereIn('period_id', $period)
      ->where('version_id', $version)
      ->orderBy('budgets.dkre_id')
      ->orderBy('budgets.activity_type_id')
      ->groupBy('budgets.dkre_id', 'budgets.payment_balance_article_id', 'budgets.activity_type_id')
      ->get();

    return $budget->groupBy('region')->map(function ($item, $key) {
      return collect([
        'dkre' => $key,
        'activity' => $item->groupBy('activity')->map(function ($item, $key) {
          return collect([
            'name' => $key,
            'article' => $item->groupBy('article')->map(function ($item, $key) {
              return $item[0]->total;
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
            return $item->sum('total');
          })
        ]);
      })->values(),
      'total' => $budget->groupBy('article')->map(function ($item, $key) {
        return $item->sum('total');
      })
    ]))->values();
  }

  /**
   * Getting budget by period and version.
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getBudgetByGroupDkre($period, $version)
  {
    $budget = DB::table('budgets')->selectRaw("
      dkre.be as be, 
      dkre.name as dkre, 
      periods.name as period,
      vid_deyatelnosti.id as vid_deyatelnosti, 
      statya_pb.code as statya_pb, 
      ROUND(SUM(sum), 3) as sum
      ")
      ->where(['period_id' => $period, 'version_id' => $version])
      ->groupBy(['be', 'dkres', 'period', 'vid_deyatelnosti', 'payment_balance_articles'])
      ->leftJoin('dkres', 'budgets.dkre_id', '=', 'dkre.id')
      ->join('periods', 'budgets.period_id', '=', 'periods.id')
      ->join('payment_balance_articles', 'budgets.payment_balance_article_id', '=', 'statya_pb.id')
      ->join('vid_deyatelnosti', 'budgets.activity_type_id', '=', 'vid_deyatelnosti.id')
      ->get()
      ->sortBy('vid_deyatelnosti')
      ->sort()
      ->groupBy(['dkres', 'vid_deyatelnosti', 'payment_balance_articles']);

    $total = DB::table('budgets')->selectRaw("
      vid_deyatelnosti.id as vid_deyatelnosti, 
      statya_pb.code as statya_pb, 
      ROUND(SUM(sum), 3) as sum
      ")
      ->where(['period_id' => $period, 'version_id' => $version])
      ->groupBy(['vid_deyatelnosti', 'payment_balance_articles'])
      ->leftJoin('dkres', 'budgets.dkre_id', '=', 'dkre.id')
      ->join('periods', 'budgets.period_id', '=', 'periods.id')
      ->join('payment_balance_articles', 'budgets.payment_balance_article_id', '=', 'statya_pb.id')
      ->join('vid_deyatelnosti', 'budgets.activity_type_id', '=', 'vid_deyatelnosti.id')
      ->get()
      ->sortBy('vid_deyatelnosti')
      ->sort()
      ->groupBy(['vid_deyatelnosti', 'payment_balance_articles']);

    $budget = $budget->map(function ($item, $key) {
      $array = $item->map(function ($item) {
        return $item->map(function ($item) {
          return round($item[0]->sum, 3);
        });
      });

      return collect([
        'dkres' => $key,
        '63310' => round($array->pluck('63310')->sum(), 3),
        '63320' => round($array->pluck('63320')->sum(), 3),
        '63330' => round($array->pluck('63330')->sum(), 3),
        '63340' => round($array->pluck('63340')->sum(), 3),
        '63430' => round($array->pluck('63430')->sum(), 3),
        'vid_deyatelnosti' => $array
      ]);
    })->values();

    return collect([
      'budget' => $budget,
      'total' => $total
    ]);
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
