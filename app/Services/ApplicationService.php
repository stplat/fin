<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Budget;
use App\Models\PaymentBalanceArticle;
use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Finance;
use App\Models\Version;
use PhpOffice\PhpSpreadsheet\IOFactory;
use function foo\func;

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
  public function getApplications($periods, $article, $version, $version_budget, $version_involvement, $version_f22, $version_shipment)
  {
    $periodSql = implode(',', $periods);
    $application = DB::select("
    SELECT applications.dkre_id, 
    applications.activity_type_id, 
    applications.payment_balance_article_id,
    payment_balance_articles.name as article,
    activity_types.name as activity,
    dkres.region as region,
    applications.source_id,
    SUM(applications.count) finance,
    budgets.sum as budget,
    finances.sum as f22,
    shipments.sum as shipment
    
    FROM applications
    
    LEFT JOIN (
    SELECT budgets.activity_type_id, 
    budgets.payment_balance_article_general, 
    budgets.dkre_id, 
    ROUND(SUM(budgets.count) - 
    IFNULL(involvements.involve_last, 0) - 
    IFNULL(involvements.involve_current, 0) - 
    IFNULL(involvements.involve_turnover, 0) + 
    IFNULL(involvements.prepayment_current, 0) + 
    IFNULL(involvements.prepayment_next, 0) - 
    total_applications.sum, 3) as sum
    
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
    
    LEFT JOIN
    (SELECT 
     SUM(count) as sum,  
     payment_balance_article_general, 
     activity_type_id, 
     dkre_id FROM applications
    WHERE period_id IN ($periodSql) AND version_id=$version
    GROUP BY payment_balance_article_general, activity_type_id, dkre_id
    ) total_applications 
    ON total_applications.payment_balance_article_general = budgets.payment_balance_article_general
    AND total_applications.activity_type_id = budgets.activity_type_id
    AND total_applications.dkre_id = budgets.dkre_id
    
    WHERE budgets.period_id IN ($periodSql) AND budgets.version_id=$version_budget
    GROUP BY budgets.activity_type_id, 
    budgets.payment_balance_article_general, 
    budgets.dkre_id, 
    involvements.involve_last,
    involvements.involve_current,
    involvements.involve_turnover,
    involvements.prepayment_current,
    involvements.prepayment_next) budgets
    ON applications.payment_balance_article_general = budgets.payment_balance_article_general
    AND applications.activity_type_id = budgets.activity_type_id
    AND applications.dkre_id = budgets.dkre_id
    
    LEFT JOIN (
    SELECT finances.activity_type_id, 
    finances.payment_balance_article_id, 
    finances.source_id,
    ROUND((SUM(finances.count) - 
    total_applications.sum), 3) as sum
    FROM `finances`
    
    LEFT JOIN
    (SELECT 
     SUM(count) as sum,  
     payment_balance_article_id, 
     activity_type_id, 
     source_id FROM applications
    WHERE period_id IN ($periodSql) AND version_id=$version
    GROUP BY payment_balance_article_id, activity_type_id, source_id
    ) total_applications     
    ON total_applications.payment_balance_article_id = finances.payment_balance_article_id
    AND total_applications.activity_type_id = finances.activity_type_id
    AND total_applications.source_id = finances.source_id
        
    WHERE finances.period_id IN ($periodSql) AND finances.version_id=$version_f22
    GROUP BY finances.activity_type_id, 
    finances.payment_balance_article_id, 
    finances.source_id) finances
    ON applications.payment_balance_article_id = finances.payment_balance_article_id
    AND applications.activity_type_id = finances.activity_type_id
    AND applications.source_id = finances.source_id
    
    LEFT JOIN (
    SELECT shipments.activity_type_id, 
    shipments.payment_balance_article_id, 
    shipments.source_id,
    shipments.dkre_id,
    ROUND(SUM(shipments.count) - 
    total_applications.sum, 3) as sum
    FROM `shipments`
    
    LEFT JOIN
    (SELECT 
     SUM(count) as sum,  
     payment_balance_article_id, 
     activity_type_id, 
     source_id,
     dkre_id
     FROM applications
    WHERE period_id IN ($periodSql) AND version_id=$version
    GROUP BY payment_balance_article_id, activity_type_id, source_id, dkre_id
    ) total_applications     
    ON total_applications.payment_balance_article_id = shipments.payment_balance_article_id
    AND total_applications.activity_type_id = shipments.activity_type_id
    AND total_applications.source_id = shipments.source_id
    AND total_applications.dkre_id = shipments.dkre_id
        
    WHERE shipments.period_id IN ($periodSql) AND shipments.version_id=$version_shipment
    GROUP BY shipments.activity_type_id, 
    shipments.payment_balance_article_id, 
    shipments.source_id,
    shipments.dkre_id) shipments
    ON applications.payment_balance_article_id = shipments.payment_balance_article_id
    AND applications.activity_type_id = shipments.activity_type_id
    AND applications.source_id = shipments.source_id
    AND applications.dkre_id = shipments.dkre_id
    
    JOIN payment_balance_articles
    ON applications.payment_balance_article_id = payment_balance_articles.id
    JOIN activity_types
    ON applications.activity_type_id = activity_types.id
    JOIN dkres
    ON applications.dkre_id = dkres.id
    
    WHERE applications.period_id in ($periodSql) AND applications.version_id=$version AND applications.payment_balance_article_id=$article
    GROUP BY applications.dkre_id, 
    applications.payment_balance_article_id, 
    applications.activity_type_id, 
    applications.source_id,
    budgets.sum,
    finances.sum,
    shipments.sum
    ORDER BY applications.dkre_id
    ");

    $application = collect($application)->groupBy('dkre_id')->map(function ($item) {
      return collect([
        'dkre' => count($item) ? $item[0]->region : '',
        'dkre_id' => count($item) ? $item[0]->dkre_id : '',
        'activity' => $item->groupBy('activity_type_id')->map(function ($item) {
          return collect([
            'name' => count($item) ? $item[0]->activity : '',
            'activity_id' => count($item) ? $item[0]->activity_type_id : '',
            'source' => $item->groupBy('source_id')->map(function ($item, $key) {
              return $item[0];
            })
          ]);
        })->values(),
        'total' => $item->groupBy('source_id')->map(function ($item, $key) {
          return collect([
            'f22' => round($item->sum('f22'), 3),
            'budget' => round($item->sum('budget'), 3),
            'finance' => round($item->sum('finance'), 3),
            'shipment' => round($item->sum('shipment'), 3),
          ]);
        })
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'activity' => collect($application)->groupBy('activity_type_id')->map(function ($item) {
        return collect([
          'name' => count($item) ? $item[0]->activity : '',
          'activity_id' => count($item) ? $item[0]->activity_type_id : '',
          'source' => $item->groupBy('source_id')->map(function ($item, $key) {
            return collect([
              'f22' => round($item[0]->f22, 3),
              'finance' => round($item->sum('finance'), 3),
              'budget' => round($item->sum('budget'), 3),
              'shipment' => round($item->sum('shipment'), 3),
            ]);
          })
        ]);
      })->values(),
      'total' => collect($application)->groupBy('source_id')->map(function ($item, $key) {
        return collect([
          'f22' => round($item[0]->f22, 3),
          'budget' => round($item->sum('budget'), 3),
          'finance' => round($item->sum('finance'), 3),
          'shipment' => round($item->sum('shipment'), 3),
        ]);
      })
    ]))->values();

    return $application;

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
   * Получаем список статей
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getArticles()
  {
    return PaymentBalanceArticle::whereNotBetween('code', [63411, 63418])
      ->whereNotBetween('code', [63422, 63426])->get();
  }

  /**
   * Парсим загруженный файл
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getUploadFile($file, $period)
  {
    $excel = IOFactory::load($file);

    $maxCell = $excel->getActiveSheet()->getHighestRowAndColumn();
    $data = $excel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

    return ParserInObjectExcelHelper($data, null, $period);
  }
}
