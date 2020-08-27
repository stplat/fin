<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use App\Http\Requests\Budget\BudgetAll;

class BudgetController extends Controller
{
  protected $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index()
  {
    return view('budget')->with([
      'data' => collect([
        'dkres' => $this->budgetService->getDkres(),
        'regions' => $this->budgetService->getRegions(),
        'months' => $this->budgetService->getPeriods('month'),
        'versions' => $this->budgetService->getVersions(),
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
