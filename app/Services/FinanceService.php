<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Dkre;
use App\Models\Period;
use App\Models\Shipment;
use App\Models\Version;

class FinanceService
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
  public function getShipments($periods, $version, $regions = null)
  {
    $dkres = $regions ?: Dkre::get()->pluck('id');
    $shipment = Shipment::select(DB::raw("
      dkres.region, 
      payment_balance_articles.name as article, 
      payment_balance_articles.id as article_id, 
      activity_types.code as activity, 
      sources.id as source, 
      ROUND(SUM(shipments.count), 3) as total
    "))
      ->join('dkres', 'dkres.id', '=', 'shipments.dkre_id')
      ->join('activity_types', 'activity_types.id', '=', 'shipments.activity_type_id')
      ->join('payment_balance_articles', 'payment_balance_articles.id', '=', 'shipments.payment_balance_article_id')
      ->join('sources', 'sources.id', '=', 'shipments.source_id')
      ->whereIn('period_id', $periods)
      ->where('version_id', $version)
      ->whereIn('dkre_id', $dkres)
      ->orderBy('shipments.dkre_id')
      ->orderBy('shipments.payment_balance_article_id')
      ->groupBy('shipments.dkre_id', 'shipments.payment_balance_article_id', 'shipments.activity_type_id', 'sources.id')
      ->get();

    return $shipment->groupBy('region')->map(function ($item, $key) {
      return collect([
        'dkre' => $key,
        'article' => $item->groupBy('article')->map(function ($item, $key) {
          return collect([
            'name' => $key,
            'activity' => $item->groupBy('activity')->map(function ($item, $key) {
              return $item->groupBy('source')->map(function ($item, $key) {
                return round($item->sum('total'), 3);
              });
            })
          ]);
        })->values(),
        'total' => $item->groupBy('activity')->map(function ($item) {
          return $item->groupBy('source')->map(function ($item, $key) {
            return round($item->sum('total'), 3);
          });
        })
      ]);
    })->put('', collect([
      'dkre' => 'ИТОГО',
      'article' => $shipment->groupBy('article')->map(function ($item, $key) {
        return collect([
          'id' => $item->groupBy('article_id')->keys()[0],
          'name' => $key,
          'activity' => $item->groupBy('activity')->map(function ($item, $key) {
            return $item->groupBy('source')->map(function ($item, $key) {
              return round($item->sum('total'), 3);
            });
          })
        ]);
      })->sortBy('id')->values(),
      'total' => $shipment->groupBy('activity')->map(function ($item, $key) {
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
