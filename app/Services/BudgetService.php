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
   * @param $version_involvement integer
   * @param $regions array
   *
   * @return \Illuminate\Support\Collection
   */
  public function getBudget($periods, $version, $version_involvement, $regions = null)
  {
    $dkreSql = $regions ? implode(',', $regions) : implode(',', Dkre::get()->pluck('id')->toArray());
    $periodSql = implode(',', $periods);
    $budget = DB::select("
      SELECT budgets.activity_type_id,
      budgets.payment_balance_article_general as article,
      activity_types.name as activity,
      dkres.region,
      budgets.dkre_id,
      SUM(budgets.count) as budget,
      involvements.involve_last,
      involvements.involve_current,
      involvements.involve_turnover,
      involvements.prepayment_current,
      involvements.prepayment_next,
      ROUND(SUM(budgets.count) -
      IFNULL(involvements.involve_last, 0) -
      IFNULL(involvements.involve_current, 0) -
      IFNULL(involvements.involve_turnover, 0) +
      IFNULL(involvements.prepayment_current, 0) +
      IFNULL(involvements.prepayment_next, 0), 3) as sum
      
      FROM `budgets`
      
      LEFT JOIN
      (SELECT
       dkre_id,
       activity_type_id,
       payment_balance_article_general,
       SUM(involvements.involve_by_prepayment_last_year) as involve_last,
       SUM(involvements.involve_by_prepayment_current_year) as involve_current,
       SUM(involvements.involve_by_turnover) as involve_turnover,
       SUM(involvements.prepayment_current_year) as prepayment_current,
       SUM(involvements.prepayment_next_year) as prepayment_next FROM `involvements`
      WHERE period_id in ($periodSql) AND version_id=$version_involvement
      GROUP BY involvements.activity_type_id, involvements.payment_balance_article_general, involvements.dkre_id
      ) involvements
      ON involvements.dkre_id = budgets.dkre_id
      AND involvements.activity_type_id = budgets.activity_type_id
      AND involvements.payment_balance_article_general = budgets.payment_balance_article_general    
      
      JOIN activity_types ON budgets.activity_type_id = activity_types.id
      JOIN dkres ON budgets.dkre_id = dkres.id
      
      WHERE budgets.period_id IN ($periodSql) AND budgets.version_id=$version AND budgets.dkre_id IN ($dkreSql)
      GROUP BY budgets.activity_type_id,
      budgets.payment_balance_article_general,
      budgets.dkre_id,
      involvements.involve_last,
      involvements.involve_current,
      involvements.involve_turnover,
      involvements.prepayment_current,
      involvements.prepayment_next
    ");

    return collect($budget)->groupBy('dkre_id')->map(function ($item, $key) {
      return collect([
        'dkre' => count($item) ? $item[0]->region : '',
        'dkre_id' => count($item) ? $item[0]->dkre_id : '',
        'activity' => $item->groupBy('activity_type_id')->map(function ($item) {
//          dd($item->toArray());
//          dd($item->groupBy('article')->map(function ($item) {
//           return $item[0]->budget;
//          })->toArray());
          return collect([
            'name' => count($item) ? $item[0]->activity : '',
            'activity_id' => count($item) ? $item[0]->activity_type_id : '',
            'involve_last' => round($item->sum('involve_last'), 3),
            'involve_current' => round($item->sum('involve_current'), 3),
            'involve_turnover' => round($item->sum('involve_turnover'), 3),
            'prepayment_current' => round($item->sum('prepayment_current'), 3),
            'prepayment_next' => round($item->sum('prepayment_next'), 3),
            'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
            'finance' => round($item->sum('sum'), 3),
            'article' => $item->groupBy('article')->map(function ($item) {
              return $item[0]->budget;
            }),
          ]);
        })->values(),
        'total' => collect([
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
          'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
          'finance' => round($item->sum('sum'), 3),
          'article' => $item->groupBy('article')->map(function ($item) {
            return round($item->sum('budget'), 3);
          }),
        ])
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => collect($budget)->groupBy('activity_type_id')->map(function ($item) {
        return collect([
          'name' => count($item) ? $item[0]->activity : '',
          'activity_id' => count($item) ? $item[0]->activity_type_id : '',
          'involve_last' => round($item->sum('involve_last'), 3),
          'involve_current' => round($item->sum('involve_current'), 3),
          'involve_turnover' => round($item->sum('involve_turnover'), 3),
          'prepayment_current' => round($item->sum('prepayment_current'), 3),
          'prepayment_next' => round($item->sum('prepayment_next'), 3),
          'finance_material' => round($item->where('article', '63400')->sum('sum'), 3),
          'finance' => round($item->sum('sum'), 3),
          'budget' => round($item->sum('budget'), 3),
          'article' => $item->groupBy('article')->map(function ($item) {
            return round($item->sum('budget'), 3);
          }),
        ]);
      })->values(),
      'total' => collect([
        'involve_last' => round(collect($budget)->sum('involve_last'), 3),
        'involve_current' => round(collect($budget)->sum('involve_current'), 3),
        'involve_turnover' => round(collect($budget)->sum('involve_turnover'), 3),
        'prepayment_current' => round(collect($budget)->sum('prepayment_current'), 3),
        'prepayment_next' => round(collect($budget)->sum('prepayment_next'), 3),
        'finance_material' => round(collect($budget)->where('article', '63400')->sum('sum'), 3),
        'finance' => round(collect($budget)->sum('sum'), 3),
        'article' => collect($budget)->groupBy('article')->map(function ($item) {
          return round($item->sum('budget'), 3);
        }),
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
