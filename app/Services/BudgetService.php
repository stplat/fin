<?php

namespace App\Services;

use App\Models\Dkre;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;


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
    $budget = DB::table('budgets')->selectRaw("
      dkre.be as be, 
      dkre.name as dkre, 
      periods.name as period,
      vid_deyatelnosti.id as vid_deyatelnosti, 
      statya_pb.code as statya_pb, 
      ROUND(SUM(sum), 3) as sum
      ")
      ->where(['period_id' => $period, 'version_id' => $version])
      ->groupBy(['be', 'dkre', 'period', 'vid_deyatelnosti', 'statya_pb'])
      ->leftJoin('dkre', 'budgets.dkre_id', '=', 'dkre.id')
      ->join('periods', 'budgets.period_id', '=', 'periods.id')
      ->join('statya_pb', 'budgets.statya_pb_id', '=', 'statya_pb.id')
      ->join('vid_deyatelnosti', 'budgets.vid_deyatelnosti_id', '=', 'vid_deyatelnosti.id')
      ->get()
      ->sortBy('vid_deyatelnosti')
      ->sort()
      ->groupBy(['dkre', 'vid_deyatelnosti', 'statya_pb']);

    $total = DB::table('budgets')->selectRaw("
      vid_deyatelnosti.id as vid_deyatelnosti, 
      statya_pb.code as statya_pb, 
      ROUND(SUM(sum), 3) as sum
      ")
      ->where(['period_id' => $period, 'version_id' => $version])
      ->groupBy(['vid_deyatelnosti', 'statya_pb'])
      ->leftJoin('dkre', 'budgets.dkre_id', '=', 'dkre.id')
      ->join('periods', 'budgets.period_id', '=', 'periods.id')
      ->join('statya_pb', 'budgets.statya_pb_id', '=', 'statya_pb.id')
      ->join('vid_deyatelnosti', 'budgets.vid_deyatelnosti_id', '=', 'vid_deyatelnosti.id')
      ->get()
      ->sortBy('vid_deyatelnosti')
      ->sort()
      ->groupBy(['vid_deyatelnosti', 'statya_pb']);

    $budget = $budget->map(function ($item, $key) {
      $array = $item->map(function ($item) {
        return $item->map(function ($item) {
          return round($item[0]->sum, 3);
        });
      });

      return collect([
        'dkre' => $key,
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
}
