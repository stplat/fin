<?php

namespace App\Http\Controllers;

use App\Models\Involvement;
use App\Services\BudgetService;
use App\Http\Requests\Budget\BudgetAll;
use App\Http\Requests\Budget\BudgetUpdate;

class BudgetController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index()
  {
//    dd($this->budgetService->getBudget([1], 2, 3)->toArray());
    return view('budget')->with([
      'data' => collect([
        'dkres' => $this->budgetService->getDkres(),
        'regions' => $this->budgetService->getRegions(),
        'periods' => $this->budgetService->getPeriods(),
        'versions' => $this->budgetService->getVersions(),
      ])
    ]);
  }

  public function all(BudgetAll $request)
  {
    $regions = $request->input('regions');
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;
    $version_involvement = $request->input('version_involvement');

    return $this->budgetService->getBudget($periods, $version, $version_involvement, $regions);
  }

  public function update(BudgetUpdate $request)
  {
    $region = $request->input('region');
    $regions = $request->input('regions') ?: null;
    $period = $request->input('period');
    $periods = $request->input('periods');
    $version = $request->input('version');
    $version_involvement = $request->input('version_involvement');

    Involvement::where('period_id', $period)
      ->where('version_id', $version_involvement)
      ->where('dkre_id', $region)
      ->where('activity_type_id', $request->input('activity'))
      ->where('payment_balance_article_general', $request->input('article'))
      ->update([
        $request->input('param') => $request->input('value')
      ]);

    return $this->budgetService->getBudget($periods, $version, $version_involvement, $regions);
  }
}
