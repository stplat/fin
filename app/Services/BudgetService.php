<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Dkre;

class BudgetService
{

  public function __construct()
  {
  }

  /**
   * Getting budget by period and version.
   *
   * @param $period integer
   * @param $version integer
   * @return \Illuminate\Support\Collection
   */
  public function getBudget($period, $version)
  {
    $budget = Dkre::with(['budget' => function ($item) use ($period, $version) {
      $item->with('vid_deyatelnosti', 'statya_pb', 'version')
        ->where('period_id', $period)
        ->where('version_id', $version);
    }])->get()
      ->map(function ($item) {
        $budget = $item->budget->reduce(function ($carry, $item) {

          /* Если предыдущий элемент не пустой и уже содержит такой вид деятельности */
          if (count($carry) && array_key_exists($item->vid_deyatelnosti->id, $carry)) {
            /* В данный вид деятельности записываем новую статью ПБ */
            $carry[$item->vid_deyatelnosti->id][$item->statya_pb->code] = $item->sum;
            return $carry;
          }

          /*Создаем новый вид деятельности для ДКРЭ*/
          $carry[$item->vid_deyatelnosti->id] = [
            'vid_deyatelnosti_id' => $item->vid_deyatelnosti->id,
            'vid_deyatelnosti' => $item->vid_deyatelnosti->name,
            'version' => $item->version->name,
            $item->statya_pb->code => $item->sum
          ];

          return $carry;
        }, []);
        return collect([
          'be' => $item->be,
          'name' => $item->name,
          'zavod' => $item->zavod,
          'budget' => collect($budget)->sortBy('vid_deyatelnosti_id')
        ]);
      });

    return $budget;
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
    $budget = \DB::table('budgets')->selectRaw("
      dkre.be as be, 
      dkre.name as dkre, 
      periods.name as period,
      budgets.vid_deyatelnosti_id as vid_deyatelnosti, 
      statya_pb.code as statya_pb, 
      SUM(sum) as sum
      ")
      ->where(['period_id' => $period, 'version_id' => $version])
      ->groupBy(['be', 'dkre', 'period', 'vid_deyatelnosti', 'statya_pb'])
      ->leftJoin('dkre', 'budgets.dkre_id', '=', 'dkre.id')
      ->join('periods', 'budgets.period_id', '=', 'periods.id')
      ->join('statya_pb', 'budgets.statya_pb_id', '=', 'statya_pb.id')
      ->get()
      ->sortBy('vid_deyatelnosti')
      ->sort()
      ->groupBy(['dkre', 'vid_deyatelnosti', 'statya_pb']);

    return $budget->map(function ($item, $key) {
      return collect([
        'dkre' => $key,
        'vid_deyatelnosti' => $item->map(function ($item) {
          return $item->map(function ($item) {
            return $item[0]->sum;
          });
        })
      ]);
    })->values();
  }
}
