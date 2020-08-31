<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use App\Http\Requests\Budget\BudgetAll;

class FinanceController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index()
  {
//    dd($this->budgetService->getBudget([1], 2)->toArray());
    return view('finance')->with([
      'data' => collect([
//        'dkres' => $this->budgetService->getDkres(),
//        'regions' => $this->budgetService->getRegions(),
//        'months' => $this->budgetService->getPeriods('month'),
//        'versions' => $this->budgetService->getVersions(),
      ])
    ]);
  }

  public function all(BudgetAll $request)
  {
    $regions = $request->input('regions');
    $periods = $request->input('periods');
    $version = $request->input('version') ?: null;

    return $this->budgetService->getBudget($periods, $version, $regions);
  }
}
