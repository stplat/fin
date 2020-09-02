<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Finance;
use App\Models\Version;

class ApplicationService
{

  /**
   * Getting budget by period and version.
   *
   * @param $periods array
   * @param $version integer
   *
   * @return \Illuminate\Support\Collection
   */
  public function getApplications($periods, $version)
  {
    $periodSql = implode(',', $periods);
    $application = DB::table('applications')
      ->join(DB::raw(
        "(SELECT dkre_id, activity_type_id, SUM(budgets.count) as budget FROM budgets WHERE period_id IN ($periodSql) AND version_id=$version GROUP BY dkre_id, activity_type_id) budgets"),
        'applications.dkre_id', '=', 'budgets.dkre_id'
      )
//      ->join(DB::raw(
//        "(SELECT payment_balance_article_id, SUM(finances.count) as finance FROM finances WHERE period_id IN ($periodSql) AND version_id=$version GROUP BY payment_balance_article_id) finances"),
//        'applications.payment_balance_article_id', '=', 'finances.payment_balance_article_id'
//      )
      ->whereIn('applications.period_id', $periods)
      ->where('applications.version_id', $version)
      ->selectRaw('
        applications.dkre_id, 
        SUM(count) as count, 
        budgets.budget,
        applications.payment_balance_article_id,
        budgets.activity_type_id,
        applications.source_id,
        ')
      ->groupBy('applications.dkre_id', 'applications.payment_balance_article_id', 'applications.source_id', 'applications.activity_type_id')
      ->get();

//    $budget = DB::raw('SELECT dkre_id, SUM(budgets.count)  FROM budgets WHERE period_id=1 AND version_id=2 GROUP BY dkre_id');

    return $application;
    $finances = Application::select(DB::raw("
      payment_balance_articles.name as article, 
      payment_balance_articles.id as article_id, 
      activity_types.code as activity,
      sources.id as source, 
      ROUND(SUM(applications.count), 3) as total
    "))
      ->join('activity_types', 'activity_types.id', '=', 'applications.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.id', '=', 'applications.payment_balance_article_id')
      ->join('sources', 'sources.id', '=', 'applications.source_id')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->orderBy('applications.payment_balance_article_id')
      ->groupBy('applications.payment_balance_article_id', 'applications.activity_type_id', 'sources.id')
      ->get();

    return $finances;

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
}
